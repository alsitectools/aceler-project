# File: .github/workflows/workflow.yml

on: [push]

name: Run Azure Login without subscription

jobs:

  build-and-deploy:
    runs-on: ubuntu-latest
    steps:

    - name: Azure Login
      uses: azure/login@v2
      with:
        client-id: ${{ secrets.AZURE_CLIENT_ID }}
        tenant-id: ${{ secrets.AZURE_TENANT_ID }}
        allow-no-subscriptions: true
        enable-AzPSSession: true

    - name: Azure CLI script
      uses: azure/cli@v2
      with:
        azcliversion: latest
        inlineScript: |
          az account show

    - name: Run Azure PowerShell
      uses: azure/powershell@v2
      with:
        azPSVersion: "latest"
        inlineScript: |
          Get-AzContext
