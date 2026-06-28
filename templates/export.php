<?php

declare(strict_types=1);

/** @var OCA\DpaTracker\Db\Subprocessor[] $subprocessors */
/** @var string $exportDate */
/** @var string $userId */

?><!DOCTYPE html>
<html lang="de">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Verarbeitungsverzeichnis – Art. 30 DSGVO</title>
<style>
*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

body {
	font-family: 'Helvetica Neue', Arial, sans-serif;
	font-size: 11pt;
	color: #1a1a1a;
	background: #fff;
	padding: 32px 40px;
}

.page-header {
	display: flex;
	justify-content: space-between;
	align-items: flex-start;
	border-bottom: 2px solid #1a1a1a;
	padding-bottom: 12px;
	margin-bottom: 24px;
}

.page-header h1 {
	font-size: 16pt;
	font-weight: 700;
	line-height: 1.2;
}

.page-header .meta {
	text-align: right;
	font-size: 9pt;
	color: #555;
	line-height: 1.6;
}

.legal-note {
	font-size: 9pt;
	color: #555;
	margin-bottom: 20px;
	padding: 8px 12px;
	border-left: 3px solid #666;
	background: #f8f8f8;
}

.count {
	font-size: 9pt;
	color: #555;
	margin-bottom: 12px;
}

table {
	width: 100%;
	border-collapse: collapse;
	font-size: 9.5pt;
}

thead th {
	background: #1a1a1a;
	color: #fff;
	padding: 7px 9px;
	text-align: left;
	font-weight: 600;
	font-size: 8.5pt;
	text-transform: uppercase;
	letter-spacing: 0.04em;
}

tbody tr:nth-child(even) { background: #f5f5f5; }

tbody td {
	padding: 8px 9px;
	vertical-align: top;
	border-bottom: 1px solid #ddd;
	line-height: 1.4;
}

.td-name { font-weight: 600; min-width: 120px; }
.td-purpose, .td-categories { max-width: 160px; }
.td-location { min-width: 90px; }

.risk-yes { color: #c0392b; font-weight: 700; }
.risk-no  { color: #27ae60; }

.empty {
	text-align: center;
	padding: 32px;
	color: #888;
	font-style: italic;
}

.footer {
	margin-top: 24px;
	padding-top: 10px;
	border-top: 1px solid #ccc;
	font-size: 8pt;
	color: #888;
	display: flex;
	justify-content: space-between;
}

/* Print button — hidden in print */
.print-bar {
	position: fixed;
	top: 0; left: 0; right: 0;
	background: #1a1a1a;
	color: #fff;
	padding: 10px 24px;
	display: flex;
	align-items: center;
	gap: 16px;
	z-index: 100;
}

.print-bar span { font-size: 10pt; }

.btn-print {
	background: #fff;
	color: #1a1a1a;
	border: none;
	padding: 6px 18px;
	font-size: 10pt;
	font-weight: 600;
	cursor: pointer;
	border-radius: 3px;
}

.btn-print:hover { background: #e8e8e8; }

@media print {
	.print-bar { display: none !important; }
	body { padding: 0; }
	thead th { background: #000 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
	tbody tr:nth-child(even) { background: #f0f0f0 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
}
</style>
</head>
<body>

<div class="print-bar">
	<span>Verarbeitungsverzeichnis – Art. 30 DSGVO</span>
	<button class="btn-print" id="btn-print">Drucken / Als PDF speichern</button>
</div>

<div style="margin-top:48px">
	<div class="page-header">
		<div>
			<h1>Verarbeitungsverzeichnis<br>nach Art. 30 DSGVO</h1>
			<div style="margin-top:6px;font-size:9.5pt;color:#555">Auftragsverarbeiter und Unterauftragnehmer</div>
		</div>
		<div class="meta">
			<div><strong>Exportiert am:</strong> <?php p($exportDate); ?></div>
			<div><strong>Nutzer:</strong> <?php p($userId); ?></div>
			<div style="margin-top:4px;font-size:8pt">Erstellt mit DPA Tracker – studio255.de</div>
		</div>
	</div>

	<p class="legal-note">
		Dieses Dokument dient als Nachweis gemäß Art. 30 Abs. 2 DSGVO über eingesetzte Auftragsverarbeiter
		und deren Unterauftragnehmer. Es ist auf dem aktuellen Stand zu halten und der Aufsichtsbehörde
		auf Anfrage vorzulegen.
	</p>

	<p class="count">
		Anzahl Auftragsverarbeiter: <strong><?php p(count($subprocessors)); ?></strong>
	</p>

	<?php if (empty($subprocessors)): ?>
		<p class="empty">Noch keine Auftragsverarbeiter erfasst.</p>
	<?php else: ?>
	<table>
		<thead>
			<tr>
				<th>#</th>
				<th>Name</th>
				<th>Verarbeitungszweck</th>
				<th>Kategorien pers. Daten</th>
				<th>Standort / Rechtssitz</th>
				<th>US-Mutterges. (CLOUD Act)</th>
				<th>Prüfdatum</th>
				<th>AVV-Dokument</th>
				<th>Erfasst am</th>
			</tr>
		</thead>
		<tbody>
		<?php $i = 1;
		foreach ($subprocessors as $sp): ?>
			<tr>
				<td><?php p($i++); ?></td>
				<td class="td-name"><?php p($sp->getName()); ?></td>
				<td class="td-purpose"><?php p($sp->getPurpose() ?? '—'); ?></td>
				<td class="td-categories"><?php p($sp->getDataCategories() ?? '—'); ?></td>
				<td class="td-location"><?php p($sp->getLocation() ?? '—'); ?></td>
				<td>
					<?php if ($sp->getUsParent()): ?>
						<span class="risk-yes">Ja ⚠</span>
					<?php else: ?>
						<span class="risk-no">Nein</span>
					<?php endif; ?>
				</td>
				<td><?php p($sp->getReviewDate() ?? '—'); ?></td>
				<td><?php p($sp->getDpaFileName() ?? '—'); ?></td>
				<td><?php p($sp->getCreatedAt() ? substr((string)$sp->getCreatedAt(), 0, 10) : '—'); ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
	<?php endif; ?>

	<div class="footer">
		<span>DPA Tracker – Verarbeitungsverzeichnis Art. 30 DSGVO</span>
		<span>Stand: <?php p($exportDate); ?> – Nutzer: <?php p($userId); ?></span>
	</div>
</div>

<script>
document.getElementById('btn-print')?.addEventListener('click', function() { window.print(); });
</script>
</body>
</html>
