{{/*
Routes every path of each host to the Service. TLS ends at the ingress controller: the images
serve plain HTTP on their port 80, as they do behind Dokploy's Traefik.
*/}}
{{- define "lychen-app.ingress" -}}
{{- $fullname := include "lychen-app.fullname" . -}}
{{- $hosts := .Values.ingress.hosts -}}
{{- if not $hosts -}}
{{- fail "ingress.hosts must list at least one host when the ingress is enabled" -}}
{{- end -}}
apiVersion: networking.k8s.io/v1
kind: Ingress
metadata:
  name: {{ $fullname }}
  labels:
    {{- include "lychen-app.labels" . | nindent 4 }}
  {{- with .Values.ingress.annotations }}
  annotations:
    {{- toYaml . | nindent 4 }}
  {{- end }}
spec:
  {{- with .Values.ingress.className }}
  ingressClassName: {{ . }}
  {{- end }}
  {{- if .Values.ingress.tls.enabled }}
  tls:
    - hosts:
        {{- toYaml $hosts | nindent 8 }}
      secretName: {{ .Values.ingress.tls.secretName | default (printf "%s-tls" $fullname) }}
  {{- end }}
  rules:
    {{- range $hosts }}
    - host: {{ . | quote }}
      http:
        paths:
          - path: /
            pathType: Prefix
            backend:
              service:
                name: {{ $fullname }}
                port:
                  name: http
    {{- end }}
{{- end -}}
