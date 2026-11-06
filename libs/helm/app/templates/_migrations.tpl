{{/*
A pre-install / pre-upgrade hook: Helm runs it with the new image and configuration before it
touches the Deployment, and fails the release when it fails, leaving the running version in
place. The Job is deleted once it succeeded, kept for its logs when it failed.
*/}}
{{- define "lychen-app.migrations" -}}
apiVersion: batch/v1
kind: Job
metadata:
  name: {{ include "lychen-app.fullname" . }}-migrations
  labels:
    {{- include "lychen-app.labels" . | nindent 4 }}
    app.kubernetes.io/component: migrations
  annotations:
    helm.sh/hook: pre-install,pre-upgrade
    helm.sh/hook-delete-policy: before-hook-creation,hook-succeeded
spec:
  activeDeadlineSeconds: {{ .Values.migrations.activeDeadlineSeconds }}
  # A failed migration is a failed deploy, not something to retry blindly.
  backoffLimit: 0
  template:
    metadata:
      labels:
        {{- include "lychen-app.selectorLabels" . | nindent 8 }}
        app.kubernetes.io/component: migrations
    spec:
      {{- include "lychen-app.podBase" . | nindent 6 }}
      restartPolicy: Never
      containers:
        - name: migrations
          {{- include "lychen-app.containerBase" . | nindent 10 }}
          {{- with .Values.migrations.args }}
          args:
            {{- toYaml . | nindent 12 }}
          {{- end }}
{{- end -}}
