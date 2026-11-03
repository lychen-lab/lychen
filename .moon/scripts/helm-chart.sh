#!/usr/bin/env bash
#
# Build a project's Helm chart and, with `push`, publish it to the OCI registry:
# the CI half of the Helm pipeline. Used by the helm-package and helm-push tasks
# (.moon/tasks/tag-helm.yml).
#
#   helm-chart.sh package <chart-dir>   lint the chart, package it into build/helm/
#   helm-chart.sh push <chart-dir>      the same, then push the package
#
# A chart is packaged for one image: its appVersion is the image tag it deploys,
# so a chart version always deploys the same image, as an image tag always holds
# the same build.
#
#   IMAGE_TAG      the image to deploy: a commit SHA, or a release vX.Y.Z
#   CHART_VERSION  the chart's version: 0.0.0-g<sha> for a commit (a pre-release,
#                  which Helm never picks as the latest version), X.Y.Z for a release
#
# Both are required to push. A local package defaults to the image of HEAD.
#
# Optional env: HELM_REGISTRY (default oci://ghcr.io/lychen-lab/lychen/charts)
set -euo pipefail

ACTION="${1:?usage: helm-chart.sh package|push <chart-dir>}"
CHART_DIR="${2:?usage: helm-chart.sh package|push <chart-dir>}"
REGISTRY="${HELM_REGISTRY:-oci://ghcr.io/lychen-lab/lychen/charts}"
DESTINATION="$(git rev-parse --show-toplevel)/build/helm"

if [ "$ACTION" = push ]; then
  : "${IMAGE_TAG:?IMAGE_TAG is required to push a chart}"
  : "${CHART_VERSION:?CHART_VERSION is required to push a chart}"
fi
IMAGE_TAG="${IMAGE_TAG:-$(git rev-parse HEAD)}"
CHART_VERSION="${CHART_VERSION:-0.0.0-g${IMAGE_TAG}}"

echo "> Vendoring the library chart"
helm dependency update "$CHART_DIR"

# --strict fails on warnings too. The second pass turns on every optional
# resource, so that their templates are checked as well, not only those the
# project's own values enable.
echo "> Linting"
helm lint "$CHART_DIR" --strict
helm lint "$CHART_DIR" --strict \
  --set autoscaling.enabled=true \
  --set ingress.enabled=true \
  --set 'ingress.hosts[0]=lint.example.org' \
  --set ingress.tls.enabled=true \
  --set migrations.enabled=true \
  --set podDisruptionBudget.enabled=true

echo "> Packaging version ${CHART_VERSION} for image ${IMAGE_TAG}"
mkdir -p "$DESTINATION"
NAME="$(helm show chart "$CHART_DIR" | sed -n 's/^name: //p')"
helm package "$CHART_DIR" --version "$CHART_VERSION" --app-version "$IMAGE_TAG" --destination "$DESTINATION"

if [ "$ACTION" = push ]; then
  echo "> Pushing to ${REGISTRY}"
  helm push "${DESTINATION}/${NAME}-${CHART_VERSION}.tgz" "$REGISTRY"
fi
