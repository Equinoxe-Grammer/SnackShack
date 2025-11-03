param(
    [string]$Name = 'phpdesktop*',
    [int]$IntervalSec = 2,
    [string]$CsvPath
)

# Simple monitor for Windows PowerShell 5.1
# Tracks CPU%, WorkingSet, PrivateMemory and writes optionally to CSV

$ErrorActionPreference = 'SilentlyContinue'
$prev = @{}  # pid => @{ cpu=$cpuTotalSec; t=$timestamp }
$cores = [int]([Environment]::GetEnvironmentVariable('NUMBER_OF_PROCESSORS'))
if (-not $cores -or $cores -lt 1) { $cores = 1 }

if ($CsvPath) {
    if (-not (Test-Path -LiteralPath (Split-Path -Parent $CsvPath))) {
        New-Item -ItemType Directory -Path (Split-Path -Parent $CsvPath) -Force | Out-Null
    }
    if (-not (Test-Path -LiteralPath $CsvPath)) {
        'time,name,pid,cpu_pct,cpu_s,ws_mb,pm_mb,threads,handles' | Out-File -FilePath $CsvPath -Encoding utf8
    }
}

Write-Host "Monitoring processes matching '$Name' every $IntervalSec s (cores=$cores). Press Ctrl+C to stop." -ForegroundColor Cyan

while ($true) {
    $now = Get-Date
    $procs = Get-Process -Name $Name -ErrorAction SilentlyContinue | Sort-Object Id
    if (-not $procs) {
        Write-Host ("{0:HH:mm:ss} waiting for process '{1}'..." -f $now, $Name) -ForegroundColor DarkGray
        Start-Sleep -Seconds $IntervalSec
        continue
    }

    foreach ($p in $procs) {
        $pid = $p.Id
        $cpuTotal = [double]($p.CPU)  # seconds total since start
        $wsMB = [math]::Round($p.WorkingSet64 / 1MB, 1)
        $pmMB = [math]::Round($p.PrivateMemorySize64 / 1MB, 1)
        $threads = $p.Threads.Count
        $handles = $p.Handles

        $cpuPct = 0.0
        if ($prev.ContainsKey($pid)) {
            $dt = [double]((New-TimeSpan -Start $prev[$pid].t -End $now).TotalSeconds)
            $dCPU = $cpuTotal - [double]$prev[$pid].cpu
            if ($dt -gt 0) {
                # CPU% across all cores
                $cpuPct = [math]::Round(($dCPU / ($dt * $cores)) * 100.0, 1)
                if ($cpuPct -lt 0) { $cpuPct = 0 }
            }
        }
        $prev[$pid] = @{ cpu = $cpuTotal; t = $now }

        $line = "{0:HH:mm:ss},{1},{2},{3},{4},{5},{6},{7},{8}" -f $now, $p.ProcessName, $pid, $cpuPct, ([math]::Round($cpuTotal,1)), $wsMB, $pmMB, $threads, $handles
        if ($CsvPath) { Add-Content -Path $CsvPath -Value $line }
        $color = if ($cpuPct -ge 80 -or $wsMB -ge 1024) { 'Red' } elseif ($cpuPct -ge 40 -or $wsMB -ge 512) { 'Yellow' } else { 'Green' }
        Write-Host $line -ForegroundColor $color
    }

    Start-Sleep -Seconds $IntervalSec
}
