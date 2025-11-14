$ErrorActionPreference = "Stop"
$link = $args[0]
$folder = $args[1]

try {
    New-Item -Path $link -ItemType SymbolicLink -Value $folder -Force | Out-Null
    Write-Host "Lien symbolique créé."
}
catch {
    Write-Error "Impossible de créer le lien symbolique : $($_.Exception.Message)"
    exit 1
}