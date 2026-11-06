{{- define "lychen-app.service" -}}
apiVersion: v1
kind: Service
metadata:
  name: {{ include "lychen-app.fullname" . }}
  labels:
    {{- include "lychen-app.labels" . | nindent 4 }}
    app.kubernetes.io/component: web
spec:
  type: {{ .Values.service.type }}
  ports:
    - name: http
      port: {{ .Values.service.port }}
      targetPort: http
      protocol: TCP
  selector:
    {{- include "lychen-app.webSelectorLabels" . | nindent 4 }}
{{- end -}}
