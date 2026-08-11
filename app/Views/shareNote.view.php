<div id="shareModal" class="modal">
    <div class="modal-content">
        <form id="shareForm" action="share" method="POST">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h2>Share task</h2>
            </div>
            <div class="modal-body">
                <p class="share-hint mb-2">Enter the email of the TackPad user to share this task with.
                    A copy appears in their account.</p>
                <div class="field-row">
                    <label class="field-label" for="share_email">Email</label>
                    <input type="email" id="share_email" name="email" placeholder="name@example.com">
                </div>
                <p class="share-feedback" id="share_feedback" hidden></p>
            </div>
            <div class="modal-footer">
                <div class="select-button">
                    <button class="btn-primary" type="submit"><i class="fas fa-share"></i>&nbsp;Share</button>
                    <button class="btn-danger" type="reset">Discard</button>
                </div>
            </div>
        </form>
    </div>
</div>
