@include('web.header')

<style>
    .shipment-cart-section {
        padding: 110px 0 80px;
        background:
            radial-gradient(ellipse 900px 400px at 12% 0%, rgba(14, 143, 122, .05), transparent 60%),
            var(--ot-bg);
    }

    .cart-page-head {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 18px;
        margin-bottom: 26px;
    }

    .cart-eyebrow {
        color: var(--ot-amber-dark);
        font-size: 12.5px;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .cart-page-head h2 {
        margin: 5px 0 0;
        font-size: 32px;
        font-weight: 700;
        letter-spacing: .01em;
    }

    .cart-subtitle {
        color: var(--ot-muted);
        margin: 8px 0 0;
        font-size: 14px;
    }

    .cart-stat-grid {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: 14px;
        margin-bottom: 30px;
    }

    .cart-stat {
        background: var(--ot-panel);
        border: 1px solid var(--ot-line);
        border-radius: 12px;
        padding: 16px 18px;
        box-shadow: var(--ot-shadow-sm);
    }

    .cart-stat span {
        color: var(--ot-muted);
        display: block;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-bottom: 8px;
    }

    .cart-stat strong {
        display: block;
        font-family: var(--ot-mono);
        font-size: 22px;
        line-height: 1.1;
    }

    .cart-section-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 14px;
        margin-bottom: 18px;
    }

    .cart-section-head h3 {
        font-size: 20px;
        font-weight: 700;
        margin: 0;
    }

    .cart-section-count {
        color: var(--ot-muted);
        font-size: 12.5px;
        font-weight: 600;
    }

    .cart-section-empty {
        background: var(--ot-panel);
        border: 1px dashed var(--ot-line);
        border-radius: 14px;
        padding: 28px 24px;
        text-align: center;
        margin-bottom: 40px;
    }

    .cart-section-empty p {
        color: var(--ot-muted);
        margin: 0 0 14px;
        font-size: 14px;
    }

    .cart-section-block {
        margin-bottom: 46px;
    }

    .cart-section-block:last-child {
        margin-bottom: 0;
    }

    .cart-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) 352px;
        gap: 24px;
        align-items: start;
    }

    @media (max-width: 991px) {
        .cart-grid { grid-template-columns: minmax(0, 1fr); }
        .cart-summary-panel { position: static !important; }
        .cart-stat-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }

    .cart-list {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .cart-item-card {
        background: var(--ot-panel);
        border: 1px solid var(--ot-line);
        border-radius: 16px;
        box-shadow: var(--ot-shadow-sm);
        overflow: hidden;
        transition: box-shadow .2s ease;
    }

    .cart-item-card:hover {
        box-shadow: var(--ot-shadow);
    }

    @media (prefers-reduced-motion: no-preference) {
        .cart-item-card {
            opacity: 0;
            transform: translateY(14px);
            transition: opacity .5s ease, transform .5s cubic-bezier(.22, .9, .3, 1), box-shadow .2s ease;
        }

        .cart-item-card.in-view {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .cart-item-top {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        flex-wrap: wrap;
        padding: 16px 20px;
        background: linear-gradient(180deg, var(--ot-panel-tint), transparent);
        border-bottom: 1px solid var(--ot-line);
    }

    .cart-item-title {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        min-width: 0;
    }

    .cart-shipment-name-row {
        align-items: center;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    .cart-item-title h5 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
        overflow-wrap: anywhere;
    }

    .cart-route-chip {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 12px;
        font-weight: 600;
        color: var(--ot-ink-soft);
        background: var(--ot-bg);
        border: 1px solid var(--ot-line);
        padding: 4px 10px 4px 8px;
        border-radius: 999px;
    }

    .cart-route-chip svg { width: 12px; height: 12px; stroke: var(--ot-muted); }

    .cart-shipment-count {
        color: var(--ot-muted);
        font-size: 12px;
        font-weight: 600;
        margin-top: 4px;
    }

    .cart-pending-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        font-weight: 700;
        letter-spacing: .03em;
        text-transform: uppercase;
        color: var(--ot-gold);
        background: var(--ot-gold-bg);
        border: 1px solid var(--ot-gold-line);
        padding: 5px 11px;
        border-radius: 999px;
        white-space: nowrap;
    }

    .cart-pending-badge svg { width: 12px; height: 12px; stroke: var(--ot-gold); }

    .cart-shipment-side {
        align-items: flex-end;
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-left: auto;
        min-width: 260px;
    }

    .cart-price {
        text-align: right;
        font-family: var(--ot-mono);
        font-weight: 700;
        font-size: 19px;
        white-space: nowrap;
    }

    .cart-price span {
        display: block;
        font-family: Corbel, 'Segoe UI', sans-serif;
        font-weight: 600;
        font-size: 11px;
        letter-spacing: .04em;
        text-transform: uppercase;
        color: var(--ot-muted);
        margin-bottom: 3px;
    }

    .cart-min-note {
        display: block;
        font-size: 11px;
        font-weight: 600;
        color: var(--ot-muted);
        margin-top: 2px;
    }

    .cart-error {
        background: #FEF3F2;
        color: #B42318;
        border: 1px solid #FECDCA;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 700;
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .cart-actions-row {
        display: flex;
        gap: 9px;
        flex-wrap: wrap;
    }

    .cart-btn {
        font-family: var(--ot-display);
        font-size: 12.5px;
        font-weight: 600;
        letter-spacing: .02em;
        border-radius: 9px;
        padding: 8px 14px;
        border: 1px solid transparent;
        display: inline-flex;
        align-items: center;
        gap: 7px;
        cursor: pointer;
        white-space: nowrap;
        transition: transform .15s ease, background .15s ease, color .15s ease, border-color .15s ease;
    }

    .cart-btn svg { width: 13px; height: 13px; stroke: currentColor; }

    .cart-btn:hover { transform: translateY(-1px); }

    .cart-cancel-shipment-btn {
        background: #FEF3F2;
        border-color: #FECDCA;
        color: #B42318;
    }

    .cart-cancel-shipment-btn:hover {
        background: #B42318;
        border-color: #B42318;
        color: #fff;
    }

    .cart-book-shipment-btn {
        background: linear-gradient(135deg, var(--ot-green), var(--ot-green-dark));
        border-color: transparent;
        color: #fff;
        box-shadow: 0 10px 22px rgba(14, 143, 122, .25);
    }

    .cart-book-shipment-btn:hover {
        box-shadow: 0 14px 26px rgba(14, 143, 122, .32);
    }

    .cart-route-row {
        align-items: center;
        display: flex;
        gap: 10px;
        justify-content: space-between;
        margin: 16px 20px 14px;
        padding: 12px 16px;
        background: var(--ot-bg);
        border: 1px solid var(--ot-line);
        border-radius: 10px;
    }

    .cart-city {
        font-weight: 700;
        min-width: 0;
        overflow-wrap: anywhere;
    }

    .cart-route-arrow {
        color: var(--ot-amber);
        flex: 0 0 auto;
        display: flex;
    }

    .cart-route-arrow svg { width: 18px; height: 18px; stroke: currentColor; }

    .cart-meta-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 10px;
        margin: 0 20px 16px;
    }

    .cart-meta {
        border-left: 2px solid var(--ot-line);
        padding: 2px 0 2px 10px;
    }

    .cart-meta span {
        color: var(--ot-muted);
        display: block;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .03em;
        margin-bottom: 4px;
    }

    .cart-meta strong {
        display: block;
        font-size: 13.5px;
        font-weight: 700;
        overflow-wrap: anywhere;
    }

    .text-ready { color: var(--ot-green-dark) !important; }
    .text-review { color: #B42318 !important; }

    .cart-item-list {
        display: flex;
        flex-direction: column;
    }

    .item-row {
        display: grid;
        grid-template-columns: 48px minmax(0, 1.6fr) auto auto auto 56px;
        align-items: center;
        gap: 14px;
        padding: 14px 20px;
        border-top: 1px solid var(--ot-line);
    }

    .ship-group.is-pending .item-row { opacity: .8; }

    @media (max-width: 720px) {
        .item-row {
            grid-template-columns: 44px 1fr;
            grid-template-areas:
                "thumb info"
                "thumb weight"
                "thumb basis"
                "thumb price";
            row-gap: 8px;
        }
        .item-row .item-actions { display: none; }
    }

    .item-thumb {
        width: 46px;
        height: 46px;
        border-radius: 11px;
        background: var(--ot-panel-tint);
        border: 1px solid var(--ot-line);
        display: grid;
        place-items: center;
        flex: none;
    }

    .item-thumb svg { width: 24px; height: 24px; stroke: var(--ot-green-dark); fill: none; }

    .item-info .item-name {
        font-weight: 700;
        font-size: 14px;
        overflow-wrap: anywhere;
    }

    .item-info .item-type {
        font-size: 11px;
        color: var(--ot-muted);
        text-transform: uppercase;
        letter-spacing: .04em;
        margin-top: 2px;
    }

    .item-specs {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 7px;
        margin-top: 6px;
        font-size: 12px;
        color: var(--ot-muted);
    }

    .item-specs .mono { color: var(--ot-ink-soft); font-family: var(--ot-mono); }

    .item-specs .sep {
        width: 3px;
        height: 3px;
        border-radius: 50%;
        background: var(--ot-line);
    }

    .weight-compare {
        display: flex;
        gap: 12px;
    }

    .metric {
        display: flex;
        flex-direction: column;
        gap: 2px;
        font-size: 10.5px;
        color: var(--ot-muted);
        min-width: 60px;
        position: relative;
    }

    .metric span { text-transform: uppercase; letter-spacing: .05em; font-size: 9.5px; }
    .metric b { font-family: var(--ot-mono); font-size: 13px; color: var(--ot-ink-soft); font-weight: 700; }
    .metric.winner b { color: var(--ot-green-dark); }

    .metric.winner::after {
        content: "billed";
        position: absolute;
        top: -12px;
        left: 0;
        font-size: 8px;
        font-weight: 800;
        letter-spacing: .08em;
        text-transform: uppercase;
        color: var(--ot-green);
    }

    .basis-chip {
        font-size: 10px;
        font-weight: 800;
        letter-spacing: .04em;
        text-transform: uppercase;
        padding: 5px 9px;
        border-radius: 999px;
        white-space: nowrap;
        display: inline-block;
    }

    .basis-chip.volume { background: var(--ot-panel-tint); color: var(--ot-green-dark); border: 1px solid rgba(14, 143, 122, .3); }
    .basis-chip.weight { background: rgba(47, 143, 224, .12); color: var(--ot-sky); border: 1px solid rgba(47, 143, 224, .3); }

    .item-price {
        text-align: right;
        white-space: nowrap;
    }

    .item-price .amount {
        font-family: var(--ot-mono);
        font-weight: 700;
        font-size: 14.5px;
    }

    .item-price .rate {
        display: block;
        font-size: 10px;
        color: var(--ot-muted);
        margin-top: 2px;
    }

    .cart-row-error {
        color: #B42318;
        font-weight: 700;
        font-size: 12px;
        cursor: help;
        display: inline-flex;
        align-items: center;
        gap: 5px;
    }

    .item-actions {
        display: flex;
        gap: 6px;
        justify-content: flex-end;
    }

    .icon-btn {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--ot-line);
        background: var(--ot-panel);
        color: var(--ot-muted);
        text-decoration: none;
        transition: all .15s ease;
    }

    .icon-btn svg { width: 13px; height: 13px; stroke: currentColor; }

    .icon-btn:hover { border-color: var(--ot-green); color: var(--ot-green-dark); }
    .icon-btn.danger:hover { border-color: #B42318; color: #B42318; background: #FEF3F2; }

    .ship-group-foot {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        flex-wrap: wrap;
        padding: 13px 20px;
        background: var(--ot-bg);
        border-top: 1px solid var(--ot-line);
        font-size: 12px;
        color: var(--ot-muted);
    }

    .ship-group-foot b {
        font-family: var(--ot-mono);
        color: var(--ot-ink);
        font-size: 14px;
    }

    .cart-summary-panel {
        position: sticky;
        top: 96px;
        background: var(--ot-panel);
        border: 1px solid var(--ot-line);
        border-radius: 16px;
        box-shadow: var(--ot-shadow);
        padding: 22px;
    }

    .cart-summary-panel h4 {
        margin: 0 0 16px;
        font-size: 17px;
        font-weight: 600;
    }

    .cart-summary-line {
        align-items: center;
        color: var(--ot-ink-soft);
        display: flex;
        justify-content: space-between;
        gap: 14px;
        font-size: 13.5px;
        margin-bottom: 11px;
    }

    .cart-summary-line strong {
        font-family: var(--ot-mono);
        color: var(--ot-ink);
    }

    .cart-summary-total {
        border-top: 1px dashed var(--ot-line);
        margin-top: 14px;
        padding-top: 16px;
    }

    .cart-summary-total span {
        color: var(--ot-muted);
        display: block;
        font-size: 12px;
        font-weight: 600;
    }

    .cart-summary-total strong {
        display: block;
        font-family: var(--ot-mono);
        font-size: 28px;
        font-weight: 700;
        line-height: 1.15;
        margin-top: 3px;
    }

    .cart-summary-actions {
        display: grid;
        gap: 10px;
        margin-top: 20px;
    }

    .btn-cta {
        width: 100%;
        justify-content: center;
        background: linear-gradient(135deg, var(--ot-amber), var(--ot-amber-dark));
        color: #fff !important;
        font-family: var(--ot-display);
        font-size: 14.5px;
        font-weight: 600;
        letter-spacing: .02em;
        padding: 13px 18px;
        border-radius: 11px;
        border: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        box-shadow: 0 14px 28px rgba(255, 122, 69, .3);
        cursor: pointer;
        transition: transform .15s ease, box-shadow .15s ease, opacity .15s ease;
    }

    .btn-cta:hover { transform: translateY(-1px); box-shadow: 0 18px 32px rgba(255, 122, 69, .36); }
    .btn-cta:disabled { opacity: .5; cursor: not-allowed; transform: none; box-shadow: none; }
    .btn-cta svg { width: 15px; height: 15px; stroke: #fff; }

    .cart-secondary-btn {
        align-items: center;
        border: 1px solid var(--ot-line);
        border-radius: 10px;
        color: var(--ot-ink);
        display: inline-flex;
        font-family: var(--ot-display);
        font-weight: 600;
        font-size: 13.5px;
        gap: 8px;
        justify-content: center;
        min-height: 44px;
        text-decoration: none;
    }

    .cart-secondary-btn svg { width: 14px; height: 14px; stroke: currentColor; }
    .cart-secondary-btn:hover { background: var(--ot-panel-tint); color: var(--ot-green-dark); }

    .cart-checkout-note {
        font-size: 12px;
        line-height: 1.55;
        color: var(--ot-muted);
        margin-top: 16px;
    }

    .cart-empty-state {
        background: var(--ot-panel);
        border: 1px solid var(--ot-line);
        border-radius: 16px;
        box-shadow: var(--ot-shadow-sm);
        padding: 56px 24px;
        text-align: center;
    }

    .cart-empty-state i {
        align-items: center;
        background: var(--ot-panel-tint);
        border-radius: 50%;
        color: var(--ot-green-dark);
        display: inline-flex;
        font-size: 34px;
        height: 76px;
        justify-content: center;
        margin-bottom: 18px;
        width: 76px;
    }

    .cart-empty-state h4 {
        font-weight: 600;
        margin-bottom: 8px;
    }

    .cart-empty-state p {
        color: var(--ot-muted);
        margin: 0 auto 22px;
        max-width: 460px;
        font-size: 14px;
    }

    @media (max-width: 575px) {
        .cart-page-head { align-items: stretch; flex-direction: column; }
        .cart-page-head h2 { font-size: 26px; }
        .cart-stat-grid { grid-template-columns: 1fr; }
        .cart-section-head { align-items: stretch; flex-direction: column; }
        .cart-item-top { align-items: stretch; flex-direction: column; }
        .cart-shipment-side { align-items: flex-start; margin-left: 0; min-width: 0; }
        .cart-route-row { align-items: flex-start; flex-direction: column; }
        .cart-meta-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); margin-left: 20px; margin-right: 20px; }
    }

    .cart-mode-switch {
        display: inline-flex;
        gap: 6px;
        background: var(--ot-panel);
        border: 1px solid var(--ot-line);
        border-radius: 12px;
        padding: 6px;
        margin-bottom: 28px;
        box-shadow: var(--ot-shadow-sm);
    }

    .cart-mode-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-family: var(--ot-display);
        font-size: 13.5px;
        font-weight: 600;
        letter-spacing: .02em;
        color: var(--ot-muted);
        background: transparent;
        border: 0;
        border-radius: 8px;
        padding: 10px 18px;
        cursor: pointer;
        transition: background .15s ease, color .15s ease;
    }

    .cart-mode-btn svg { width: 15px; height: 15px; stroke: currentColor; }

    .cart-mode-btn:hover { color: var(--ot-ink); }

    .cart-mode-btn.active {
        background: linear-gradient(135deg, var(--ot-green), var(--ot-green-dark));
        color: #fff;
        box-shadow: 0 8px 18px rgba(14, 143, 122, .25);
    }

    .cart-mode-count {
        font-family: var(--ot-mono);
        font-size: 11px;
        font-weight: 700;
        background: rgba(0, 0, 0, .08);
        border-radius: 999px;
        padding: 1px 8px;
    }

    .cart-mode-btn.active .cart-mode-count { background: rgba(255, 255, 255, .25); }

    .cart-section-block[data-cart-panel] { display: none; }
    .cart-section-block[data-cart-panel].active { display: block; }
</style>

<svg style="position:absolute; width:0; height:0; overflow:hidden" aria-hidden="true">
    <symbol id="ico-arrow" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 12h16M14 6l6 6-6 6"></path></symbol>
    <symbol id="ico-clock" viewBox="0 0 24 24" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="8.5"></circle><path d="M12 7.5V12l3 2"></path></symbol>
    <symbol id="ico-pin" viewBox="0 0 24 24" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s6.5-6.2 6.5-11A6.5 6.5 0 1 0 5.5 10c0 4.8 6.5 11 6.5 11z"></path><circle cx="12" cy="10" r="2.2"></circle></symbol>
    <symbol id="ico-shield" viewBox="0 0 24 24" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3l7 3v6c0 4.4-3 7.5-7 9-4-1.5-7-4.6-7-9V6z"></path><path d="M9 12l2 2 4-4"></path></symbol>
    <symbol id="ico-check" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"></path></symbol>
    <symbol id="ico-edit" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 013 3L7 19l-4 1 1-4z"></path></symbol>
    <symbol id="ico-trash" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 7h16"></path><path d="M9 7V4h6v3"></path><path d="M6 7l1 13h10l1-13"></path></symbol>
    <symbol id="ico-x" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6L6 18M6 6l12 12"></path></symbol>
    <symbol id="ico-plus" viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"></path></symbol>
    <symbol id="ico-alert" viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 9v4M12 17h.01"></path><path d="M10.3 3.9L2.9 17a1.8 1.8 0 001.6 2.7h15a1.8 1.8 0 001.6-2.7L13.7 3.9a1.8 1.8 0 00-3.4 0z"></path></symbol>
    <symbol id="ico-box" viewBox="0 0 32 32" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M4 10l12-6 12 6-12 6z"></path><path d="M4 10v12l12 6 12-6V10"></path><path d="M16 16v12"></path></symbol>
    <symbol id="ico-bike" viewBox="0 0 32 32" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="8" cy="22" r="5"></circle><circle cx="24" cy="22" r="5"></circle><path d="M8 22l6-11h6l4 11"></path><path d="M14 11h6"></path><path d="M14 22h10"></path></symbol>
    <symbol id="ico-electronics" viewBox="0 0 32 32" fill="none" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="6" width="24" height="16" rx="1.6"></rect><path d="M12 26h8M16 22v4"></path></symbol>
    <symbol id="ico-warehouse" viewBox="0 0 24 24" fill="none" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21V9l9-6 9 6v12"></path><path d="M7 21v-8h10v8"></path></symbol>
</svg>

@php
    $totalShipmentItems = $shipmentCartItems->count();
    $totalWarehouseItems = $warehouseCartItems->count();
    $totalRequests = $shipmentCartItems->groupBy(fn ($item) => implode('|', [$item->city_route_id, optional($item->pickup_date)->format('Y-m-d'), optional($item->delivery_date)->format('Y-m-d')]))->count()
        + $warehouseCartItems->groupBy(fn ($item) => implode('|', [$item->warehouse_id, optional($item->pickup_date)->format('Y-m-d')]))->count();
    $combinedTotal = $shipmentCartTotal + $warehouseCartTotal;
    $bothEmpty = $shipmentCartItems->isEmpty() && $warehouseCartItems->isEmpty();
@endphp

<section class="shipment-cart-section">
    <div class="container">
        <div class="cart-page-head">
            <div>
                <span class="cart-eyebrow">Your Cart</span>
                <h2>Shipment &amp; Warehouse Requests</h2>
                <p class="cart-subtitle">Review both your shipment and storage items together, and track either from the same place.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if($bothEmpty)
            <div class="cart-empty-state">
                <i class="bi bi-box-seam"></i>
                <h4>Your cart is empty</h4>
                <p>Add a shipment or a warehouse storage item to see it here.</p>
                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <a href="{{ route('shipment.add_item') }}" class="primary-btn1 btn-hover">
                        Add Shipment
                        <span></span>
                    </a>
                    <a href="{{ route('warehouse.add_item') }}" class="primary-btn1 btn-hover">
                        Store an Item
                        <span></span>
                    </a>
                </div>
            </div>
        @else
            <div class="cart-stat-grid">
                <div class="cart-stat">
                    <span>Requests</span>
                    <strong>{{ $totalRequests }}</strong>
                </div>
                <div class="cart-stat">
                    <span>Total Items</span>
                    <strong>{{ $totalShipmentItems + $totalWarehouseItems }}</strong>
                </div>
                <div class="cart-stat">
                    <span>Combined Estimated Total</span>
                    <strong>₹{{ number_format($combinedTotal, 2) }}</strong>
                </div>
            </div>

            @php $activeCartTab = $activeCartTab ?? 'shipment'; @endphp
            <div class="cart-mode-switch" role="tablist">
                <button type="button" class="cart-mode-btn {{ $activeCartTab === 'shipment' ? 'active' : '' }}" data-cart-tab="shipment" role="tab" aria-selected="{{ $activeCartTab === 'shipment' ? 'true' : 'false' }}">
                    <svg viewBox="0 0 24 24"><use href="#ico-pin"></use></svg>
                    Transport
                    @if($totalShipmentItems > 0)<span class="cart-mode-count">{{ $totalShipmentItems }}</span>@endif
                </button>
                <button type="button" class="cart-mode-btn {{ $activeCartTab === 'warehouse' ? 'active' : '' }}" data-cart-tab="warehouse" role="tab" aria-selected="{{ $activeCartTab === 'warehouse' ? 'true' : 'false' }}">
                    <svg viewBox="0 0 24 24"><use href="#ico-warehouse"></use></svg>
                    Warehouse
                    @if($totalWarehouseItems > 0)<span class="cart-mode-count">{{ $totalWarehouseItems }}</span>@endif
                </button>
            </div>

            <div class="cart-section-block {{ $activeCartTab === 'shipment' ? 'active' : '' }}" data-cart-panel="shipment">
                @include('web.partials.cart-section', ['items' => $shipmentCartItems, 'total' => $shipmentCartTotal, 'mode' => 'shipment'])
            </div>

            <div class="cart-section-block {{ $activeCartTab === 'warehouse' ? 'active' : '' }}" data-cart-panel="warehouse">
                @include('web.partials.cart-section', ['items' => $warehouseCartItems, 'total' => $warehouseCartTotal, 'mode' => 'warehouse'])
            </div>
        @endif
    </div>
</section>

<script>
    (function () {
        var cards = document.querySelectorAll('.cart-item-card');

        cards.forEach(function (card, index) {
            var delay = 70 + Math.min(index * 90, 360);
            setTimeout(function () {
                card.classList.add('in-view');
            }, delay);
        });
    })();

    (function () {
        var buttons = document.querySelectorAll('.cart-mode-btn');
        var panels = document.querySelectorAll('[data-cart-panel]');

        if (!buttons.length) return;

        buttons.forEach(function (btn) {
            btn.addEventListener('click', function () {
                var tab = btn.getAttribute('data-cart-tab');

                buttons.forEach(function (b) {
                    var isActive = b === btn;
                    b.classList.toggle('active', isActive);
                    b.setAttribute('aria-selected', isActive ? 'true' : 'false');
                });

                panels.forEach(function (panel) {
                    panel.classList.toggle('active', panel.getAttribute('data-cart-panel') === tab);
                });
            });
        });
    })();
</script>

@include('web.footer')
