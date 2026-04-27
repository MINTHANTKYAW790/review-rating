<style>
    .tea-cta {
        background: rgba(11, 11, 187, 0.67);
        border: 1px solid rgba(104, 104, 172, 0.25);
        color: white;
        transition: all .2s ease-in-out;
    }

    .tea-cta:hover {
        background: rgba(20, 20, 236, 0.74);
        border-color: rgba(104, 104, 172, 0.45);
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 10px rgba(104, 104, 172, 0.25);
    }
s
    .tea-icon-wrap {
        width: 30px;
        height: 30px;
        background: #6868AC;
        color: #fff;
    }

    .tea-icon {
        animation: teaWiggle 1.4s ease-in-out infinite;
        transform-origin: 60% 70%;
        display: inline-block;
    }

    @keyframes teaWiggle {
        0%,
        100% {
            transform: rotate(0deg) scale(1);
        }

        20% {
            transform: rotate(-11deg) scale(1.08);
        }

        40% {
            transform: rotate(8deg) scale(1.1);
        }

        60% {
            transform: rotate(-6deg) scale(1.05);
        }

        80% {
            transform: rotate(4deg) scale(1.03);
        }
    }
</style>

<footer class="main-footer px-3 py-2" style="border-top: 1px solid rgba(104, 104, 172, 0.15);">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center w-100">
        <div class="text-muted mb-2 mb-md-0">
            <strong style="color: #6868AC;">TechBit</strong>
            <span class="ml-1">| Copyright &copy; 2025</span>
        </div>

        <a href="https://support-min-thant-kyaw.vercel.app/" target="_blank" rel="noopener noreferrer"
            class="tea-cta d-inline-flex align-items-center text-decoration-none px-2 py-1 rounded"
            title="Buy me a tea"
            aria-label="Buy me a tea">
            <span class="tea-icon-wrap d-inline-flex align-items-center justify-content-center rounded-circle mr-2">
                <i class="tea-icon fas fa-mug-hot" aria-hidden="true"></i>
            </span>
            <span style="font-weight: 600;">Buy me a tea</span>
        </a>
    </div>
</footer>