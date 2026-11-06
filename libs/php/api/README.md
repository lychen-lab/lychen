# lychen/api

API Platform building blocks shared by the Lychen APIs, for resources kept apart from the
Doctrine model with Symfony's object mapper (`#[Map]`), whose relations are exposed and
written by uuid rather than by IRI.

## The uuid contract

An entity linked by uuid needs an int id for the database, a mapped `uuid` field for the
API, and the contract of `Interface\UuidIdentifiedInterface` (`getUuid(): Uuid`).

lychen/api does not require lychen/model, and lychen/model does not require lychen/api:
each declares its own, identical `UuidIdentifiedInterface`. `UuidContract` therefore checks
the contract by its shape: an entity fulfills it when it implements this library's
interface, or has a `getUuid()` returning a `Uuid`, whichever interface declares it. The
entities built on lychen/model's `AbstractIdOrmAndUuidApiIdentified` fulfill it as they
are.

## What it holds

| Class | Role |
| --- | --- |
| `Transform\RelationToUuid` | Entity -> resource: exposes a relation as a uuid (an entity) or a list of uuids (a collection). |
| `Transform\UuidToEntity` | Input DTO -> entity: resolves the uuid(s) sent by the client into the entities to link. |
| `Validator\UuidExists` | Reports every unknown uuid as a 422 on its property (`seedLibrary`, `characteristics[1]`), before any mapping. |
| `Doctrine\MappedAssociationResolver` | Finds the entity a DTO property refers to, from the `#[Map]` and Doctrine mappings. |
| `Doctrine\UuidEntityFinder` | Loads entities by uuid, one query per call. |
| `Trait\*` | Common properties of a resource class: `uuid` (identifier), `createdAt`, `updatedAt`, `code`, `state`. |
| `Interface\UuidIdentifiedInterface` | The contract of an entity identified by uuid. |
| `UuidContract` | Checks that contract by its shape (`isFulfilledBy()`), reads an entity's uuid (`uuidOf()`). |
| `LychenApiBundle` | Registers the services, with the tags the validator factory and the object mapper need. |

## Usage

```php
// The resource: mapped from the entity, relations exposed by uuid.
#[ApiResource(stateOptions: new Options(entityClass: SeedLotEntity::class))]
#[Post(input: SeedLotPost::class, collectDenormalizationErrors: true)]
#[Map(source: SeedLotEntity::class)]
final class SeedLot
{
    use UuidIdentifierTrait;

    #[Map(source: 'seedLibrary', transform: RelationToUuid::class)]
    public ?Uuid $seedLibrary = null;

    /** @var list<Uuid> */
    #[Map(source: 'characteristics', transform: RelationToUuid::class)]
    public array $characteristics = [];
}

// The input DTO: mapped onto the entity, relations written by uuid.
#[Map(target: SeedLotEntity::class)]
final class SeedLotPost
{
    #[Assert\NotNull]
    #[UuidExists]
    #[Map(transform: UuidToEntity::class)]
    public Uuid $seedLibrary;

    /** @var list<Uuid> */
    #[UuidExists]
    #[Map(transform: UuidToEntity::class)]
    public array $characteristics = [];
}
```

Neither `#[UuidExists]` nor `UuidToEntity` names the entity: both read it from the
Doctrine association the property is mapped onto (the DTO's `#[Map(target: Entity::class)]`,
then the property's name, or its `#[Map(target: 'otherName')]`). On a DTO that is not
mapped onto an entity, name it: `#[UuidExists(entity: SeedLibrary::class)]`.

`RelationToUuid` and `UuidToEntity` are transform services: reference them by class name.
`RelationToUuid` also has static `one()` and `many()`, usable as
`[RelationToUuid::class, 'one']`, but the object mapper only calls such an array when
`is_callable()` accepts it. With a wrong class (a stale `use` after a move) or method name,
it reads the array as a list of transform services and fails with
`"...RelationToUuid" is not a valid callable`.

A list replaces the collection through the entity's adder and remover
(`addCharacteristic()` / `removeCharacteristic()`), so `[]` unlinks them all.

flora-api's `SeedLibrary`, `SeedLot` and `Characteristic` resources are the full example,
with their POST and PATCH DTOs and functional tests.

## Wiring an API

1. `composer.json`: require `"lychen/api": "*"` and add it to the `versions` of the
   `../packages/*` path repository, then `composer update lychen/api`. It needs
   symfony/object-mapper 8.1.9 or later, which turns on API Platform's mapping.
2. `moon.yml`: add `lychen-php-api` to `dependsOn`.
3. `config/bundles.php`: `Lychen\Api\LychenApiBundle::class => ['all' => true]`.

Without the bundle, the API would have to register the services itself, with the
`validator.constraint_validator` tag on `UuidExistsValidator` and the
`object_mapper.transform_callable` tag on `UuidToEntity`. Symfony instantiates an untagged
validator without any argument ("Too few arguments to function
...UuidExistsValidator::__construct()").
