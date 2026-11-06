# Kubernetes (Helm)

[Dokploy](deployment.md) remains how Lychen deploys. Next to it, every project that
builds an image also has a **Helm chart**, so that it can run on Kubernetes too:

- **CI** packages each chart, pinned to the image it deploys, and pushes it to
  `oci://ghcr.io/lychen-lab/lychen/charts/<project>`;
- **CD** runs `helm upgrade` against a Kubernetes environment, once you create one.

Until a `kubernetes-*` GitHub Environment exists, only the CI half runs: the charts
are published with every delivery, and nothing is deployed. None of this touches the
Dokploy workflows, composes or environments.

Projects with a chart (tag `helm`): `website`, `espace-website`, `espace-app`,
`espace-api`, `tera-api`, `flora-api`. The shared services (`common-rabbitmq`,
`common-mercure`, `zitadel`) and the APIs' Postgres and Redis are not part of it:
on Kubernetes they come from elsewhere (managed services, their own charts), and the
applications reach them through their configuration.

## The charts

```
libs/helm/app/                 # library chart `lychen-app`: every template, every default
  values.yaml                  # the defaults, documented
projects/<project>/helm/
  Chart.yaml                   # named after the project, depends on lychen-app
  values.yaml                  # what differs for this project, in every environment
  templates/app.yaml           # {{ include "lychen-app.all" . }}
```

A project's chart renders, from the library:

- a **Deployment** of the image on port 80, probed on `/version.txt` (every image
  serves the commit it was built from there), and its **Service**;
- optionally an **Ingress** (`ingress.enabled`, `ingress.hosts`, `ingress.tls`), a
  **HorizontalPodAutoscaler** (`autoscaling`), a **PodDisruptionBudget**
  (`podDisruptionBudget`);
- for the APIs, a **migrations Job** (`migrations`), a Helm pre-install/pre-upgrade
  hook: the new version migrates the database once, before its pods start, and a
  failed migration fails the deploy with the running version left in place.

Values are layered, last one wins: the library's `values.yaml`, then the project's
`helm/values.yaml`, then the environment's values given at deploy time. Maps merge key
by key and lists are replaced. Like any sprig merge, `false`, `0` or `''` cannot
override a default that is set, so every toggle of the library defaults to off.

The runtime configuration works as it does on Dokploy:

- **Frontends** (Caddy) read theirs from environment variables when the container
  starts (`APP_ID`, `HOST`, `ESPACE_API_HOST`, `ZITADEL_*`…, see
  [`entrypoint.sh`](../libs/docker/frontend/entrypoint.sh)): set them in `env`.
- **APIs** (FrankenPHP) listen on plain HTTP (`SERVER_NAME: ':80'`): TLS ends at the
  ingress controller. Every variable of their `.env` comes from a Secret named after
  the project (`envFrom`), which you create in the namespace. The chart never holds a
  secret.

## Versions

A chart is packaged for one image: its `appVersion` is the image tag, and the image
defaults to it. A chart version therefore always deploys the same build, as an image
tag always holds the same build.

| Built for | Chart version | Image tag (`appVersion`) | Published by |
| --- | --- | --- | --- |
| a commit of `main` | `0.0.0-g<sha>` | `<sha>` | **Helm \| Staging** |
| a release | `X.Y.Z` | `vX.Y.Z` | **Helm \| Release** |

`0.0.0-g<sha>` is a SemVer pre-release, which Helm never picks as the latest version:
only an explicit `--version` deploys it. (The `g` keeps the identifier valid when a SHA
starts with a zero and has no letter.)

An image is promoted, never rebuilt. A chart cannot be re-tagged, since its version is
written inside the package: a release chart is packaged again from the release commit,
the same sources as that commit's staging chart.

## Pipeline

| Workflow | When | Does |
| --- | --- | --- |
| [QG](../.github/workflows/qg.yml) | every PR | `helm-package` in the Build job: lints and packages every affected chart, all optional resources turned on. |
| [Helm \| Staging](../.github/workflows/helm-staging.yml) | after each **Deploy \| Staging** run | Pushes the chart of every project whose image was built for that commit, then deploys them to `kubernetes-staging` if that environment exists. |
| [Helm \| Release](../.github/workflows/helm-release.yml) | a release is published | Pushes every chart as `X.Y.Z`, pinned to `vX.Y.Z`. |
| [Helm \| Production](../.github/workflows/helm-production.yml) | manual, with a version (blank = latest) | Deploys every chart of that version to `kubernetes-production`, the image pinned by digest. |

**Helm | Staging** starts from Deploy | Staging's completion rather than from the push,
because a chart must never pin an image that does not exist: it only packages the
projects whose `:<sha>` image was pushed, whatever the outcome of the run. A project
whose build failed is caught up by the next delivery, which rebuilds it.

A deploy is `helm upgrade --install --wait --rollback-on-failure`
([`helm-deploy.sh`](../.moon/scripts/helm-deploy.sh)): Helm waits for the new pods to be
ready, and rolls the release back when they are not. There is no `/version.txt` polling
as on Dokploy, which only queues a deploy.

In production the image is pinned by digest (`image.digest`), and the pod template
carries no version label: a project whose `vX.Y.Z` image is the one already running
keeps its pods, which Deploy | Production gets by skipping the deploy.

> A change to the library chart (`libs/helm/app`) affects every project with a chart,
> as any library affects its dependents: Deploy | Staging rebuilds and redeploys them
> (on Dokploy too), and Helm | Staging publishes their new charts with the new images.

## Setting up an environment

Out of this repo, once per environment (`kubernetes-staging`, `kubernetes-production`):

1. **In the cluster:** a namespace, an ingress controller (and cert-manager for TLS,
   if you like), and in the namespace, one Secret per API holding the variables of its
   `.env`:

   ```bash
   kubectl -n lychen-staging create secret generic espace-api --from-env-file=espace-api.env
   ```

   The images and charts on ghcr.io follow the repository's visibility: if they are
   private, add a pull secret and list it in `imagePullSecrets`.

2. **A GitHub Environment** named `kubernetes-staging` or `kubernetes-production`, with:

   | Name | Kind | Value |
   | --- | --- | --- |
   | `KUBECONFIG` | secret | A kubeconfig for a service account that manages the namespace. |
   | `KUBERNETES_NAMESPACE` | variable | The namespace. Blank = the namespace of the kubeconfig's context. |
   | `<PROJECT>_HELM_VALUES` | variable | The environment's values for that project, in YAML, e.g. `ESPACE_API_HELM_VALUES`. Hosts and plain configuration, never secrets. |

   For instance, `ESPACE_APP_HELM_VALUES`:

   ```yaml
   env:
     ESPACE_API_HOST: https://api.espace.lychen.org
     ZITADEL_CLIENT_ID: '<client id>'
     ZITADEL_ISSUER: https://<zitadel host>
     ZITADEL_PROJECT_RESOURCE_ID: '<project id>'
   ingress:
     annotations:
       cert-manager.io/cluster-issuer: letsencrypt
     className: nginx
     enabled: true
     hosts:
       - app.espace.lychen.org
     tls:
       enabled: true
   ```

   A project without its `<PROJECT>_HELM_VALUES` variable fails its deploy, as a
   missing `<PROJECT>_DOKPLOY_ID` does: the others still deploy. Add a required
   reviewer to `kubernetes-production` for a second approval.

Creating `kubernetes-staging` turns the staging CD on from the next delivery of `main`.
Before that, deploy a published chart by hand to try it:

```bash
helm upgrade espace-app oci://ghcr.io/lychen-lab/lychen/charts/espace-app \
  --install --version 0.0.0-g<sha> --namespace lychen-staging --values espace-app.yaml
```

## Locally

The helm tasks need Helm 4 (`brew install helm`), and run without node:

```bash
moon espace-api:helm-package   # lint, then package into build/helm/
```

To read what a chart renders, with your own values:

```bash
helm dependency update projects/espace/api/helm
helm template espace-api projects/espace/api/helm --values my-values.yaml
```

## Rollback

Run **Helm | Production** with an older `vX.Y.Z`, as for Dokploy: its chart and image
are still on ghcr.io, nothing is rebuilt. `helm rollback <project>` also goes back to
the previous revision of one release.
