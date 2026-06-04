<div id="shareModal" class="modal">
    <div class="modal-content">
        <form id="shareForm" action="share" method="POST">
            <div class="modal-header">
                <span class="close">&times;</span>
                <h2>Share task</h2>
            </div>
            <div class="modal-body">
                <p class="share-hint">Enter the email address of the TackPad user you want to share this task with.
                    A copy will appear in their account.</p>
                <table>
                    <tr>
                        <td><label for="share_email">Email:</label></td>
                        <td><input type="email" id="share_email" name="email" placeholder="name@example.com"></td>
                    </tr>
                </table>
                <p class="share-feedback" id="share_feedback" hidden></p>
            </div>
            <div class="modal-footer">
                <div class="select-button">
                    <button class="hinzufuegen" type="submit">Share</button>
                    <button class="verwerfen" type="reset">Discard</button>
                </div>
            </div>
        </form>
    </div>
</div>
