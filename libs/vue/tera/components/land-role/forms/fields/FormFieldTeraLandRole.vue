<template>
  <FormField
    v-slot="{ componentField }"
    v-model="model"
    :validate-on-blur="!isFieldDirty"
    name="landRoles"
    :rules="fieldSchema"
  >
    <FormItem class="flex flex-col">
      <FormLabel>Rôles</FormLabel>
      <Combobox
        v-model:open="open"
        by="label"
      >
        <FormControl class="w-full">
          <ComboboxAnchor>
            <TagsInput
              :model-value="componentField.modelValue"
              class="gap-2 px-2"
              @update:model-value="componentField['onUpdate:modelValue']"
            >
              <div class="flex flex-wrap items-center gap-2">
                <TagsInputItem
                  v-for="item in componentField.modelValue"
                  :key="item['@id']"
                  :value="item"
                >
                  <TagsInputItemText class="px-2">
                    {{ item.name }}
                  </TagsInputItemText>
                  <TagsInputItemDelete />
                </TagsInputItem>
              </div>

              <ComboboxInput
                v-model="searchTerm"
                as-child
              >
                <TagsInputInput
                  placeholder="Rechercher un rôle..."
                  class="h-auto w-full min-w-[200px] border-none bg-transparent p-0 shadow-none focus-visible:ring-0"
                  @keydown.enter.prevent
                />
              </ComboboxInput>
            </TagsInput>
          </ComboboxAnchor>
        </FormControl>

        <ComboboxList>
          <ComboboxEmpty> Aucuns rôle trouvé </ComboboxEmpty>

          <ComboboxGroup>
            <ComboboxItem
              v-for="landRole in filteredOptions"
              :key="landRole.value['@id']"
              :value="landRole.value"
              @select.prevent="handleSelect(landRole.value)"
            >
              {{ landRole.label }}

              <ComboboxItemIndicator>
                <IconCheck class="ml-auto h-4 w-4" />
              </ComboboxItemIndicator>
            </ComboboxItem>
          </ComboboxGroup>
        </ComboboxList>
      </Combobox>

      <FormDescription> Select roles for this user. </FormDescription>
      <FormMessage />
    </FormItem>
  </FormField>
</template>

<script setup lang="ts">
import {
  Combobox,
  ComboboxAnchor,
  ComboboxEmpty,
  ComboboxGroup,
  ComboboxInput,
  ComboboxItem,
  ComboboxItemIndicator,
  ComboboxList,
} from '@lychen/vue-components-core/combobox';

import {
  FormControl,
  FormDescription,
  FormField,
  FormItem,
  FormLabel,
  FormMessage,
} from '@lychen/vue-components-core/form';
import { useFilter } from 'reka-ui';
import { computed, ref, onMounted } from 'vue';

import { useQuery } from '@tanstack/vue-query';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import { z } from 'zod';
import { toTypedSchema } from '@vee-validate/zod';
import {
  TagsInputItem,
  TagsInputItemDelete,
  TagsInput,
  TagsInputInput,
} from '@lychen/vue-components-core/tags-input';
import type { components } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import IconCheck from '@lychen/vue-icons/IconCheck.vue';

// Land roles reach this field from several API projections (land_role / land_member /
// land_member_invitation collections). The field only needs `@id` (selection key) and `name`
// (label), so model the minimal shape all projections satisfy. Same convention as
// CardTeraLandMemberInvitation's `landRoles` prop.
type LandRoleOption = Pick<components['schemas']['LandRole.jsonld'], '@id' | 'name'>;

const props = defineProps<{
  land: components['schemas']['Land.jsonld'];
  isFieldDirty: boolean;
  initialValues?: LandRoleOption[];
}>();

const model = defineModel<LandRoleOption[]>({ default: [] });

const fieldSchema = toTypedSchema(
  z.array(z.object({ '@id': z.string() })).min(1, {
    message: 'Please select at least one land role.',
  }),
);

const landUlid = computed(() => props.land?.ulid);

const { api } = useTeraApi();

const { data: landRoles } = useQuery({
  queryKey: ['landRoles', landUlid],
  queryFn: async () => {
    if (!landUlid.value) {
      throw new Error('missing.ulid');
    }
    const response = await api.GET('/api/land_roles', {
      params: {
        query: {
          land: landUlid.value,
        },
      },
    });
    return response.data;
  },
  enabled: !!landUlid.value,
});

const landRoleOptions = computed(() =>
  (landRoles.value?.member || []).map((landRole) => ({
    label: landRole.name,
    value: landRole,
  })),
);
const searchTerm = ref('');
const open = ref(false);
const { contains } = useFilter({ sensitivity: 'base' });

const filteredOptions = computed(() => {
  if (!landRoleOptions.value) {
    return [];
  }

  const options = landRoleOptions.value.filter(
    (i) => !model.value.some((m) => m['@id'] === i.value['@id']),
  );
  return searchTerm.value
    ? options.filter((option) => contains(option.label, searchTerm.value))
    : options;
});

function handleSelect(value: LandRoleOption) {
  searchTerm.value = '';

  model.value.push(value);
  model.value = [...new Set(model.value)];

  if (filteredOptions.value.length === 0) {
    open.value = false;
  }
}

onMounted(() => {
  if (props.initialValues) {
    model.value = [...props.initialValues];
  }
});
</script>
