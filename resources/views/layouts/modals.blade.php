 <!-- Message Modal -->
 <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="messageForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="messageModalLabel">Send Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="receiver_id" id="receiver_id">
                    <input type="hidden" name="sender_id" id="sender_id">
                    <input type="hidden" name="sender_name" id="sender_name">
                    <div class="mb-3">
                        <label for="message" class="form-label">Message</label>
                        <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Send
                        Message</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Group Message Modal -->
<div class="modal fade" id="groupMessageModal" tabindex="-1" aria-labelledby="groupMessageModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="groupMessageForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="groupMessageModalLabel">Send Group Message</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="sender_id" id="group_sender_id">
                    <input type="hidden" name="sender_name" id="group_sender_name">
                    <input type="hidden" name="group_id" id="group_id">
                    <input type="hidden" name="group_name" id="group_name">
                    <div class="mb-3">
                        <label for="group_message" class="form-label">Message</label>
                        <textarea class="form-control" id="group_message" name="message" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Send Message to all user Message Modal -->
<div class="modal fade" id="allUserMessageModal" tabindex="-1" aria-labelledby="allUserMessageModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="allUserMessageForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="allUserMessageModalLabel">Send Message To All Users</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="sender_id" id="user_sender_id">
                    <input type="hidden" name="sender_name" id="user_sender_name">
                    <div class="mb-3">
                        <label for="alluser_message" class="form-label">Message</label>
                        <textarea class="form-control" id="alluser_message" name="message" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>


