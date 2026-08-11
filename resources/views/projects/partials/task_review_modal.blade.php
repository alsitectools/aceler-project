<div class="modal fade" id="taskReviewModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Review task') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="taskReview-taskId">
                <input type="hidden" id="taskReview-projectTypeId">
                <p class="mb-2"><strong id="taskReview-taskName"></strong></p>

                <div class="d-grid gap-2">
                    <button type="button" class="btn btn-success" data-state="reviewed">{{ __('Revisado') }}</button>
                    <button type="button" class="btn btn-warning" data-state="changes">{{ __('Pedir cambio') }}</button>
                </div>

                <div class="mt-3" id="taskReview-commentWrap" style="display:none;">
                    <label for="taskReview-comment">{{ __('Comment') }}</label>
                    <textarea class="form-control" id="taskReview-comment" rows="3"
                        placeholder="{{ __('Explain the requested change...') }}"></textarea>
                    <button type="button" class="btn btn-warning w-100 mt-2"
                        id="taskReview-changesButton">{{ __('Confirm change request') }}</button>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="taskReviewDetailModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{ __('Revisar') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-2"><strong id="detail-taskName"></strong></p>

                <div class="mb-3">
                    <label class="form-label">{{ __('Change request comment') }}</label>
                    <p id="detail-comment" class="form-control-plaintext text-wrap"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label">{{ __('Requested by') }}</label>
                    <p id="detail-user" class="form-control-plaintext"></p>
                </div>

                <button type="button" class="btn btn-primary w-100" id="detail-ackBtn">{{ __('Cambio realizado') }}</button>
                <button type="button" class="btn btn-secondary w-100 mt-2" data-bs-dismiss="modal">{{ __('Cerrar') }}</button>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        const reviewRoute = '{{ route('projects.milestone.task.review', [$currentWorkspace->slug]) }}';
        const clearRoute = '{{ route('projects.milestone.task.review.clear', [$currentWorkspace->slug]) }}';

        function openTaskReviewModal(taskEl) {
            const modalEl = document.getElementById('taskReviewModal');
            if (!modalEl) return;

            const card = taskEl.closest('.milestone-card');

            document.getElementById('taskReview-taskId').value = taskEl.getAttribute('data-task-id');
            document.getElementById('taskReview-taskName').textContent = taskEl.getAttribute('data-task-name');
            document.getElementById('taskReview-projectTypeId').value = card
                ? card.getAttribute('data-project-type-id') : '';

            document.getElementById('taskReview-commentWrap').style.display = 'none';
            document.getElementById('taskReview-comment').value = '';

            const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
            modal.show();
        }

        function submitTaskReview(stateCode) {
            const modalEl = document.getElementById('taskReviewModal');
            const taskId = document.getElementById('taskReview-taskId').value;
            if (!taskId) return;

            const isClear = stateCode === 'cleared';
            const url = isClear ? clearRoute : reviewRoute;
            const data = { task_id: taskId };

            if (!isClear) {
                data.state_code = stateCode;
                if (stateCode === 'changes') {
                    data.comment = document.getElementById('taskReview-comment').value;
                    if (!data.comment.trim()) {
                        alert('{{ __('Please enter a comment for the requested change.') }}');
                        return;
                    }
                }
            }

            $.ajax({
                url: url,
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: data,
                success: function() {
                    bootstrap.Modal.getOrCreateInstance(modalEl).hide();
                    location.reload();
                },
                error: function(xhr) {
                    console.error('Error al marcar revisión:', xhr);
                    alert('{{ __('Error updating task review') }}');
                }
            });
        }

        function openTaskReviewDetailModal(btnEl) {
            const modalEl = document.getElementById('taskReviewDetailModal');
            if (!modalEl) return;

            document.getElementById('detail-taskName').textContent = btnEl.getAttribute('data-task-name');
            document.getElementById('detail-comment').textContent = btnEl.getAttribute('data-review-comment') || '—';
            document.getElementById('detail-user').textContent = btnEl.getAttribute('data-review-user') || '—';

            document.getElementById('detail-ackBtn').setAttribute('data-task-id', btnEl.getAttribute('data-task-id'));

            bootstrap.Modal.getOrCreateInstance(modalEl).show();
        }

        function clearTaskReview(taskId) {
            if (!taskId) return;
            $.ajax({
                url: clearRoute,
                type: 'POST',
                headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                data: { task_id: taskId },
                success: function() {
                    location.reload();
                },
                error: function(xhr) {
                    console.error('Error al limpiar revisión:', xhr);
                    alert('{{ __('Error updating task review') }}');
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const modalEl = document.getElementById('taskReviewModal');
            if (!modalEl) return;

            modalEl.querySelector('[data-state="reviewed"]').addEventListener('click', function() {
                submitTaskReview('reviewed');
            });
            modalEl.querySelector('[data-state="changes"]').addEventListener('click', function() {
                document.getElementById('taskReview-commentWrap').style.display = 'block';
            });
            document.getElementById('taskReview-changesButton').addEventListener('click', function() {
                submitTaskReview('changes');
            });

            document.addEventListener('click', function(e) {
                const ackBtn = e.target.closest('.task-ack-btn');
                if (!ackBtn) return;
                e.stopPropagation();
                e.preventDefault();
                openTaskReviewDetailModal(ackBtn);
            });

            document.getElementById('detail-ackBtn').addEventListener('click', function() {
                clearTaskReview(this.getAttribute('data-task-id'));
            });

            document.addEventListener('contextmenu', function(e) {
                const taskEl = e.target.closest('.milestone-task');
                if (!taskEl) return;
                e.preventDefault();
                openTaskReviewModal(taskEl);
            });
        });
    })();
</script>