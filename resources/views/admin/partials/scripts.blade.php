<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 1. SISTEM PERPINDAHAN TAB VIEW INTERAKTIF VIA SESSIONSTORAGE
    function switchMainTab(tabId) {
        document.querySelectorAll('.main-panel-content').forEach(panel => {
            panel.classList.remove('block'); panel.classList.add('hidden');
        });
        document.querySelectorAll('.main-tab-btn').forEach(btn => {
            btn.classList.remove('text-emerald-400', 'border-emerald-500'); btn.classList.add('text-slate-500', 'border-transparent');
        });
        document.getElementById('panel-' + tabId).classList.remove('hidden');
        document.getElementById('panel-' + tabId).classList.add('block');
        document.getElementById('tabBtn-' + tabId).classList.remove('text-slate-500', 'border-transparent');
        document.getElementById('tabBtn-' + tabId).classList.add('text-emerald-400', 'border-emerald-500');
        sessionStorage.setItem('activeAdminTab', tabId);
    }

    document.addEventListener('DOMContentLoaded', function() {
        const savedTab = sessionStorage.getItem('activeAdminTab');
        if (savedTab && document.getElementById('panel-' + savedTab)) {
            switchMainTab(savedTab);
        }
    });

    function toggleWarningLog(id) {
        const log = document.getElementById(id);
        log.classList.contains('hidden') ? log.classList.remove('hidden') : log.classList.add('hidden');
    }

    // 2. MODAL PREVIEW ADUAN KENDALA
    function openTicketModal(btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        const email = btn.getAttribute('data-email');
        const subject = btn.getAttribute('data-subject');
        const description = btn.getAttribute('data-description');

        document.getElementById('ticketModalName').innerText = name;
        document.getElementById('ticketModalEmail').innerText = email;
        document.getElementById('ticketModalSubject').innerText = subject;
        document.getElementById('ticketModalDescription').innerText = description;

        document.getElementById('formResolveTicket').action = `/admin/tickets/${id}/resolve`;

        const modal = document.getElementById('ticketModal');
        modal.classList.remove('hidden'); modal.classList.add('flex');
        setTimeout(() => { modal.classList.remove('opacity-0'); document.getElementById('ticketModalContent').classList.remove('scale-95'); }, 10);
    }

    function closeTicketModal() {
        const modal = document.getElementById('ticketModal');
        modal.classList.add('opacity-0'); document.getElementById('ticketModalContent').classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); }, 300);
    }

    // 3. MODAL PREVIEW VERIFIKASI DOKUMEN VENDOR
    function openReviewModal(btn) {
        const id = btn.getAttribute('data-id');
        const name = btn.getAttribute('data-name');
        const npwp = btn.getAttribute('data-npwp');
        const email = btn.getAttribute('data-email');
        const address = btn.getAttribute('data-address');
        const pdfUrl = btn.getAttribute('data-pdf');

        document.getElementById('modalCompanyName').innerText = name;
        document.getElementById('modalNpwp').innerText = npwp;
        document.getElementById('modalEmail').innerText = email;
        document.getElementById('modalAddress').innerText = address;

        const container = document.getElementById('pdfContainer');
        container.innerHTML = pdfUrl ? `<iframe src="${pdfUrl}" class="w-full h-full border-0 absolute inset-0"></iframe>` : `<div class="absolute inset-0 flex items-center justify-center text-slate-500 font-bold text-sm uppercase">Dokumen Kosong</div>`;

        document.getElementById('formApprove').action = `/admin/vendors/${id}/approve`;
        document.getElementById('formReject').action = `/admin/vendors/${id}/reject`;

        const modal = document.getElementById('reviewModal');
        modal.classList.remove('hidden'); modal.classList.add('flex');
        setTimeout(() => { modal.classList.remove('opacity-0'); document.getElementById('reviewModalContent').classList.remove('scale-95'); }, 10);
    }

    function closeReviewModal() {
        const modal = document.getElementById('reviewModal');
        modal.classList.add('opacity-0'); document.getElementById('reviewModalContent').classList.add('scale-95');
        setTimeout(() => { modal.classList.add('hidden'); modal.classList.remove('flex'); document.getElementById('pdfContainer').innerHTML = ''; }, 300);
    }

    // 4. CHART.JS INITIALIZATION
    document.addEventListener('DOMContentLoaded', function() {
        const chartElement = document.getElementById('spkluGrowthChart');
        if (chartElement) {
            const ctx = chartElement.getContext('2d');
            let gradient = ctx.createLinearGradient(0, 0, 0, 250);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.4)'); gradient.addColorStop(1, 'rgba(16, 185, 129, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! json_encode($chartLabels ?? []) !!},
                    datasets: [{
                        data: {!! json_encode($chartData ?? []) !!},
                        borderColor: '#10b981', backgroundColor: gradient, borderWidth: 3, fill: true, tension: 0.4
                    }]
                },
                options: {
                    responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });
        }
    });
</script>