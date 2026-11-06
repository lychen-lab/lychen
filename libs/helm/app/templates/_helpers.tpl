{{/*
Merges the project's values over this library's defaults, which Helm hands to the project's
chart under the `lychen-app` key. Every template of the project's chart must start with it
(lychen-app.all does); it is idempotent.
*/}}
{{- define "lychen-app.init" -}}
{{- if hasKey .Values "lychen-app" -}}
{{- $defaults := deepCopy (index .Values "lychen-app") -}}
{{- $overrides := deepCopy (omit .Values "lychen-app") -}}
{{- $_ := set . "Values" (mustMergeOverwrite $defaults $overrides) -}}
{{- end -}}
{{- end -}}

{{/*
Every resource is named after the release, which the CD names after the project.
*/}}
{{- define "lychen-app.fullname" -}}
{{- .Release.Name | trunc 63 | trimSuffix "-" -}}
{{- end -}}

{{- define "lychen-app.selectorLabels" -}}
app.kubernetes.io/name: {{ .Chart.Name }}
app.kubernetes.io/instance: {{ .Release.Name }}
{{- end -}}

{{/*
The web pods. The component keeps their selector apart from any other workload of the release
(the migrations Job today, a worker tomorrow): a Deployment's selector cannot change later.
*/}}
{{- define "lychen-app.webSelectorLabels" -}}
{{ include "lychen-app.selectorLabels" . }}
app.kubernetes.io/component: web
{{- end -}}

{{/*
Labels of the resources themselves. The pods only get the selector labels: a label that changes
with every chart version (version, chart) would roll them even when the image did not change.
*/}}
{{- define "lychen-app.labels" -}}
{{ include "lychen-app.selectorLabels" . }}
app.kubernetes.io/version: {{ .Chart.AppVersion | quote }}
app.kubernetes.io/part-of: lychen
app.kubernetes.io/managed-by: {{ .Release.Service }}
helm.sh/chart: {{ printf "%s-%s" .Chart.Name .Chart.Version | replace "+" "_" | trunc 63 | trimSuffix "-" }}
{{- end -}}

{{- define "lychen-app.image" -}}
{{- $repository := .Values.image.repository | default (printf "ghcr.io/lychen-lab/lychen/%s" .Chart.Name) -}}
{{- if .Values.image.digest -}}
{{- printf "%s@%s" $repository .Values.image.digest -}}
{{- else -}}
{{- printf "%s:%s" $repository (.Values.image.tag | default .Chart.AppVersion) -}}
{{- end -}}
{{- end -}}

{{/*
The image, environment and resources shared by the web container and the migrations Job, which
must run the same code with the same configuration.
*/}}
{{- define "lychen-app.containerBase" -}}
image: {{ include "lychen-app.image" . }}
imagePullPolicy: {{ .Values.image.pullPolicy }}
{{- with .Values.env }}
env:
  {{- range $name, $value := . }}
  - name: {{ $name }}
    {{- if kindIs "map" $value }}
    valueFrom:
      {{- toYaml $value | nindent 6 }}
    {{- else }}
    value: {{ $value | toString | quote }}
    {{- end }}
  {{- end }}
{{- end }}
{{- with .Values.envFrom }}
envFrom:
  {{- toYaml . | nindent 2 }}
{{- end }}
{{- with .Values.resources }}
resources:
  {{- toYaml . | nindent 2 }}
{{- end }}
{{- with .Values.securityContext }}
securityContext:
  {{- toYaml . | nindent 2 }}
{{- end }}
{{- end -}}

{{/*
The pod-level settings shared by the web pods and the migrations Job.
*/}}
{{- define "lychen-app.podBase" -}}
automountServiceAccountToken: false
{{- with .Values.imagePullSecrets }}
imagePullSecrets:
  {{- toYaml . | nindent 2 }}
{{- end }}
{{- with .Values.podSecurityContext }}
securityContext:
  {{- toYaml . | nindent 2 }}
{{- end }}
{{- with .Values.nodeSelector }}
nodeSelector:
  {{- toYaml . | nindent 2 }}
{{- end }}
{{- with .Values.affinity }}
affinity:
  {{- toYaml . | nindent 2 }}
{{- end }}
{{- with .Values.tolerations }}
tolerations:
  {{- toYaml . | nindent 2 }}
{{- end }}
{{- end -}}
