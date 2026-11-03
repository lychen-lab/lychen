#!/usr/bin/env bash
#
# Upgrade a project's Helm release to a chart version of the OCI registry,
# installing it on its first deploy: the CD half of the Helm pipeline, used by
# helm-staging.yml and helm-production.yml.
#
# The release is named after the project, like its chart. The environment's own
# values (hosts, configuration) come on top of the chart's, from HELM_VALUES; its
# secrets live in the cluster, never here. Helm waits for the new pods to be
# ready, and rolls the release back when they are not.
#
# Required env:
#   PROJECT        project id: the chart's name and the release's
#   CHART_VERSION  chart version to deploy
#   HELM_VALUES    the environment's values for the project (YAML)
# Optional env:
#   IMAGE_DIGEST   pin the image by digest (image.digest in the chart)
#   HELM_REGISTRY  default oci://ghcr.io/lychen-lab/lychen/charts
#
# The cluster comes from KUBECONFIG, the namespace from HELM_NAMESPACE (empty =
# the namespace of the kubeconfig's context).
set -euo pipefail

: "${PROJECT:?PROJECT is required}"
: "${CHART_VERSION:?CHART_VERSION is required}"
: "${HELM_VALUES:?HELM_VALUES is required}"
CHART="${HELM_REGISTRY:-oci://ghcr.io/lychen-lab/lychen/charts}/${PROJECT}"

VALUES_FILE="$(mktemp)"
trap 'rm -f "$VALUES_FILE"' EXIT
printf '%s\n' "$HELM_VALUES" > "$VALUES_FILE"

ARGS=(--values "$VALUES_FILE")
if [ -n "${IMAGE_DIGEST:-}" ]; then
  ARGS+=(--set "image.digest=${IMAGE_DIGEST}")
fi

echo "> Upgrading ${PROJECT} to ${CHART}:${CHART_VERSION}"
helm upgrade "$PROJECT" "$CHART" \
  --install \
  --version "$CHART_VERSION" \
  --wait \
  --rollback-on-failure \
  --timeout 15m \
  "${ARGS[@]}"
echo "> Deployed ${PROJECT} ${CHART_VERSION}${IMAGE_DIGEST:+ @ ${IMAGE_DIGEST}}"
