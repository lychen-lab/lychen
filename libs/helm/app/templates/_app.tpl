{{/*
Every resource of a Lychen application, from its values. A project's chart renders it from a
single template: `{{ include "lychen-app.all" . }}`.
*/}}
{{- define "lychen-app.all" -}}
{{- include "lychen-app.init" . -}}
{{ include "lychen-app.deployment" . }}
---
{{ include "lychen-app.service" . }}
{{- if .Values.ingress.enabled }}
---
{{ include "lychen-app.ingress" . }}
{{- end }}
{{- if .Values.autoscaling.enabled }}
---
{{ include "lychen-app.horizontalPodAutoscaler" . }}
{{- end }}
{{- if .Values.podDisruptionBudget.enabled }}
---
{{ include "lychen-app.podDisruptionBudget" . }}
{{- end }}
{{- if .Values.migrations.enabled }}
---
{{ include "lychen-app.migrations" . }}
{{- end }}
{{- end -}}
