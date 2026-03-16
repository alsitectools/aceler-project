<style>
    .my-summary-score-shell {
        max-width: 560px;
        margin: 0 auto;
        width: 100%;
    }

    .minimal-score-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.08);
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 44px 20px 36px;
        border: 1px solid #f8fafc;
    }

    .minimal-score-notch {
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 250px;
        height: 25px;
        background-color: #aa182c;
        border-bottom-left-radius: 20px;
        border-bottom-right-radius: 20px;
    }

    .minimal-score-label {
        font-size: 13px;
        font-weight: 800;
        color: #94a3b8;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .minimal-score-value {
        font-size: 64px;
        font-weight: 900;
        color: #1e293b;
        line-height: 1;
        margin: 0;
        letter-spacing: -0.02em;
    }
</style>

<div class="my-summary-score-shell">
    <div class="minimal-score-card">
        <div class="minimal-score-notch"></div>
        <div class="minimal-score-label">{{ __('score from last month') }}</div>
        <div class="minimal-score-value">{{ floatval($userScore) }}</div>
    </div>
</div>
