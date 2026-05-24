<style>
/* ─── Dashboard Header ───────────────────────────────── */
.dash-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.dash-header h1 {
    color: #2c2c2a;
    font-size: 1.75rem;
    font-weight: 600;
    margin: 0;
}

/* ─── Calamity Meter ─────────────────────────────────── */
.calamity-meter {
    background: #faeeda;
    border: 1px solid #ef9f27;
    border-radius: 6px;
    padding: 8px 12px;
    text-align: right;
    min-width: 140px;
    font-size: 0.85rem;
}

.calamity-meter .cal-label {
    font-size: 9px;
    text-transform: uppercase;
    letter-spacing: 0.8px;
    color: #b8860b;
    margin-bottom: 2px;
    font-weight: 500;
}

.calamity-meter .cal-name {
    font-size: 11px;
    font-weight: 600;
    color: #633806;
    line-height: 1.2;
}

.calamity-meter .cal-badge {
    display: inline-block;
    background: #e24b4a;
    color: #fff;
    font-size: 8px;
    padding: 2px 6px;
    border-radius: 4px;
    font-weight: 500;
    margin-top: 2px;
}

.calamity-meter.none { background: #eaf3de; border-color: #639922; }
.calamity-meter.none .cal-label { color: #3b6d11; }
.calamity-meter.none .cal-name  { color: #27500a; }

/* ─── Charts Row ─────────────────────────────────────── */
.charts-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.chart-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08);
    min-width: 0;
}

.chart-title {
    font-size: 12px;
    font-weight: 700;
    color: #2c2c2a;
    letter-spacing: 0.04em;
    margin-bottom: 0.75rem;
    text-transform: uppercase;
}

canvas { max-width: 100% !important; height: auto !important; }
.chart-card canvas { max-height: 200px; }

.chart-actions {
    display: flex;
    justify-content: flex-end;
    margin-top: 8px;
}

/* ─── Bottom Grid ────────────────────────────────────── */
.bottom-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

/* ─── Section Cards (scoped to dashboard) ────────────── */
.db-section-card {
    background: #fff !important;
    border: 1px solid #e5e7eb !important;
    border-radius: 8px !important;
    padding: 1rem 1.25rem !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.08) !important;
    min-width: 0;
    overflow: hidden;
}

.db-section-title {
    font-size: 13px !important;
    font-weight: 700 !important;
    text-transform: uppercase !important;
    letter-spacing: 0.04em !important;
    color: #2c2c2a !important;
    margin: 0 0 1rem 0 !important;
    background: transparent !important;
    padding: 0 !important;
}

/* ─── Table Scroll Wrapper ───────────────────────────── */
.db-table-scroll {
    overflow-x: auto;
    overflow-y: scroll;
    -webkit-overflow-scrolling: touch;
    width: 100%;
    max-height: 250px;
    border-radius: 6px;
    border: 1px solid #e5e7eb;
}

.db-table-scroll::-webkit-scrollbar {
    width: 6px;
}

.db-table-scroll::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.db-table-scroll::-webkit-scrollbar-thumb {
    background: #ccc;
    border-radius: 3px;
}

.db-table-scroll::-webkit-scrollbar-thumb:hover {
    background: #999;
}

/* ─── Dashboard Tables ───────────────────────────────── */
.db-table {
    width: 100%;
    min-width: 460px;
    border-collapse: collapse;
    font-size: 13px;
    background: #fff !important;
    color: #333 !important;
}

.db-table thead tr {
    background: #f8f9fa !important;
}

.db-table th {
    padding: 10px 12px;
    text-align: left;
    font-size: 12px;
    font-weight: 600;
    color: #495057 !important;
    border-bottom: 2px solid #dee2e6;
    white-space: nowrap;
    background: #f8f9fa !important;
}

.db-table td {
    padding: 10px 12px;
    border-bottom: 1px solid #f0f0f0;
    color: #333 !important;
    background: #fff !important;
    vertical-align: middle;
}

.db-table tbody tr:last-child td {
    border-bottom: none;
}

.db-link {
    color: #185fa5 !important;
    text-decoration: none;
    font-weight: 500;
}

.db-link:hover {
    text-decoration: underline;
}

/* ─── Status Badges ──────────────────────────────────── */
.relief-status-badge {
    display: inline-block;
    padding: 3px 10px;
    border-radius: 12px;
    font-size: 11px;
    font-weight: 600;
    text-transform: uppercase;
    white-space: nowrap;
}

.relief-status-badge.ongoing  { background: #fef3c7; color: #d97706; }
.relief-status-badge.upcoming { background: #dbeafe; color: #1e40af; }
.relief-status-badge.done,
.relief-status-badge.completed { background: #d1fae5; color: #059669; }

/* ─── Calamity alert badge ───────────────────────────── */
.badge-intensity {
    display: inline-block;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-left: 8px;
}

.badge-intensity.low { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
.badge-intensity.medium { background: #fff3cd; color: #856404; border: 1px solid #ffeaa7; }
.badge-intensity.high { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
.badge-intensity.critical { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
.badge-intensity.unknown { background: #e2e3e5; color: #383d41; border: 1px solid #d6d8db; }

/* ─── Stat animation ─────────────────────────────────── */
.stat-num { transition: all 0.3s ease; }
.stat-num.updating { color: #10b981; transform: scale(1.1); }

/* ─── Responsive ─────────────────────────────────────── */
@media (max-width: 1024px) {
    .charts-row  { grid-template-columns: 1fr; }
    .bottom-grid { grid-template-columns: 1fr; }
}

@media (max-width: 768px) {
    .dash-header h1 { font-size: 1.25rem; }

    .stats-row {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.5rem;
    }

    .stat-card  { padding: 0.75rem; }
    .stat-num   { font-size: 1.3rem; }
    .stat-label { font-size: 0.75rem; }

    .charts-row  { gap: 1rem; }
    .bottom-grid { gap: 1rem; }

    .db-section-card { padding: 0.875rem !important; }

    .db-table {
        min-width: 400px;
        font-size: 12px;
    }

    .db-table th,
    .db-table td {
        padding: 8px 10px;
    }
}

@media (max-width: 480px) {
    .dash-header { margin-bottom: 1.25rem; }
    .dash-header h1 { font-size: 1.1rem; }

    .stats-row {
        grid-template-columns: 1fr 1fr;
        gap: 0.4rem;
    }

    .stat-card  { padding: 0.6rem; }
    .stat-num   { font-size: 1.1rem; }
    .stat-label { font-size: 0.65rem; }

    .chart-card { padding: 0.75rem; }

    .db-section-card { padding: 0.75rem !important; }

    .db-table {
        min-width: 360px;
        font-size: 11px;
    }

    .db-table th,
    .db-table td {
        padding: 7px 8px;
    }
}
</style>
