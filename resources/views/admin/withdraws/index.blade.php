@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-8">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-semibold">Permintaan Penarikan Pedagang</h2>
    </div>

    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // Modal handling
        const modal = document.getElementById('confirm-modal');
        const modalMessage = document.getElementById('confirm-modal-message');
        const modalOk = document.getElementById('confirm-ok');
        const modalCancel = document.getElementById('confirm-cancel');

        function showModal(message) {
            return new Promise((resolve) => {
                modalMessage.textContent = message;
                modal.classList.remove('hidden');
                modal.style.display = 'flex';

                function clean() {
                    modal.classList.add('hidden');
                    modal.style.display = '';
                    modalOk.removeEventListener('click', onOk);
                    modalCancel.removeEventListener('click', onCancel);
                }

                function onOk() { clean(); resolve(true); }
                function onCancel() { clean(); resolve(false); }

                modalOk.addEventListener('click', onOk);
                modalCancel.addEventListener('click', onCancel);
            });
        }

        // Toasts
        const toastContainer = document.getElementById('toast-container');
        function showToast(message, type = 'info') {
            const el = document.createElement('div');
            el.className = 'px-4 py-2 rounded shadow text-sm text-white';
            el.style.transition = 'opacity 0.3s ease';
            if (type === 'success') el.style.background = '#16a34a';
            else if (type === 'error') el.style.background = '#dc2626';
            else el.style.background = '#0369a1';
            el.textContent = message;
            toastContainer.appendChild(el);
            setTimeout(() => { el.style.opacity = '0'; }, 2500);
            setTimeout(() => { el.remove(); }, 3000);
        }

        function badgeForStatus(status) {
            status = String(status).toLowerCase();
            if (status === 'pending') return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">Pending</span>';
            if (status === 'approved') return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">Approved</span>';
            if (status === 'rejected') return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">Rejected</span>';
            if (status === 'completed') return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Completed</span>';
            return '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">' + String(status) + '</span>';
        }

        function renderActions(status, id) {
            status = String(status).toLowerCase();
            if (status === 'pending') {
                return `<div class="inline-flex items-center gap-2">
                            <button class="action-btn approve-btn bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded" data-id="${id}" data-action="approve">Approve</button>
                            <button class="action-btn reject-btn bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded" data-id="${id}" data-action="reject">Reject</button>
                        </div>`;
            }
            if (status === 'approved') {
                return `<div class="inline-flex items-center gap-2">
                            <button class="action-btn complete-btn bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded" data-id="${id}" data-action="complete">Complete</button>
                        </div>`;
            }
            return '<span class="text-sm text-gray-500">—</span>';
        }

        async function sendAction(id, action) {
            const url = `/admin/withdraws/${id}/${action}`;
            try {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    },
                    credentials: 'same-origin'
                });
                if (!res.ok) {
                    const json = await res.json().catch(()=>({message:'Request failed'}));
                    throw new Error(json.message || 'Request failed');
                }
                return await res.json().catch(()=>({}));
            } catch (err) {
                throw err;
            }
        }

        async function handleAction(id, action) {
            let msg = 'Aksi ini akan mengubah status permintaan.';
            if (action === 'approve') msg = 'Setujui permintaan penarikan ini?';
            if (action === 'reject') msg = 'Tolak permintaan penarikan ini?';
            if (action === 'complete') msg = 'Tandai penarikan ini sebagai selesai?';

            const ok = await showModal(msg);
            if (!ok) return;

            try {
                const json = await sendAction(id, action);
                // update row in-place
                const row = document.getElementById(`withdraw-row-${id}`);
                if (!row) { showToast('Baris tidak ditemukan', 'error'); return; }
                const newStatus = (json.status) ? json.status : (action === 'approve' ? 'approved' : action === 'reject' ? 'rejected' : action === 'complete' ? 'completed' : null);
                if (newStatus) {
                    const statusCell = row.querySelector('.status-cell');
                    const actionsCell = row.querySelector('.actions-cell');
                    statusCell.innerHTML = badgeForStatus(newStatus);
                    actionsCell.innerHTML = renderActions(newStatus, id);
                    attachActionListenersForRow(row);
                }
                showToast('Aksi berhasil', 'success');
            } catch (err) {
                console.error(err);
                showToast(err.message || 'Gagal memproses aksi', 'error');
            }
        }

        function attachActionListenersForRow(row) {
            row.querySelectorAll('.action-btn').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const id = btn.dataset.id;
                    const action = btn.dataset.action;
                    handleAction(id, action);
                });
            });
        }

        // attach initial listeners
        document.querySelectorAll('.action-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                const id = btn.dataset.id;
                const action = btn.dataset.action;
                handleAction(id, action);
            });
        });

    });
    </script>
    @endpush
