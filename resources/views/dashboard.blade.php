<!DOCTYPE html>
<html>

<head>
    <title>User Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ url('/css/dashboard.css') }}">
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <!-- DataTables Bootstrap JS -->
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <!-- Bootstrap Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Custom JS -->
    <script src="{{ asset('js/app.js') }}"></script>

</head>

<body>
    <!-- Include navbar -->
    @include('layouts.navbar')
    <!-- Include modals -->
    @include('layouts.modals')
    <div class="container">

        <div class="card mb-3">
            <div class="card-body">
                <h2 class="mb-4">List of Users</h2>
                <table id="usersTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Send Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loop through users and display in table -->
                        @foreach ($users as $user)
                            <tr>
                                <td>{{ $user->id }}</td>
                                <td>{{ $user->name }}</td>
                                <td>
                                    <!-- Button to show message form -->
                                    <button class="btn btn-primary"
                                        onclick="showMessageForm({{ $user->id }}, '{{ $user->name }}', '{{ Auth::user()->id }}','{{ Auth::user()->name }}')">Send</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="container">

        <div class="card mb-3">
            <div class="card-body">
                <h2 class="mb-4">Groups</h2>
                <table id="groupsTable" class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Subscribe/Unsubscribe</th>
                            <th>Send Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loop through user groups and display in table -->
                        @foreach ($userGroups as $group)
                            <tr>
                                <td>{{ $group['id'] }}</td>
                                <td>{{ $group['name'] }}</td>
                                <td>
                                    <!-- Button to subscribe/unsubscribe from group -->
                                    <button
                                        class="btn btn-info subscribe-btn
                                        @if ($group['is_subscribed']) btn-danger @endif"
                                        data-user-id="{{ Auth::user()->id }}"
                                        data-user-name="{{ Auth::user()->name }}" data-group-id="{{ $group['id'] }}"
                                        data-is-subscribed="{{ $group['is_subscribed'] ? 'true' : 'false' }}">
                                        @if ($group['is_subscribed'])
                                            UnSubscribe
                                        @else
                                            Subscribe
                                        @endif
                                    </button>
                                </td>
                                <td>
                                    <!-- Button to send message to group -->
                                    <button class="btn btn-primary send-btn" data-group-id="{{ $group['id'] }}"
                                        onclick="showGroupMessageForm('{{ Auth::user()->id }}','{{ Auth::user()->name }}','{{ $group['id'] }}','{{ $group['name'] }}')"
                                        @if (!$group['is_subscribed']) disabled @endif>Send</button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Send message to all users -->
        <div class="p-4 bg-light rounded mb-5 flex">
            <h3>Send Message To All Users!</h3>
            <button
                class="btn btn-primary"onclick="showAllUserMessageForm('{{ Auth::user()->id }}','{{ Auth::user()->name }}')">Send</button>
        </div>
    </div>


    </div>

    <script>
        // Array to store subscribed group IDs
        let subscribedGroupIds = [];
        // Loop through userGroups to populate subscribedGroupIds
        @foreach ($userGroups as $group)
            @if ($group['is_subscribed'])
                subscribedGroupIds.push({{ $group['id'] }});
            @endif
        @endforeach
        
        // Function to show message form for individual user
        function showMessageForm(receiverId, recieverName, senderId, senderName) {
            $('#receiver_id').val(receiverId);
            $('#sender_id').val(senderId);
            $('#sender_name').val(senderName);
            $('#messageModalLabel').text('Send Message to ' + recieverName);
            $('#messageModal').modal('show');
        }

        // Function to show message form for a group
        function showGroupMessageForm(senderId, senderName, groupId, groupName) {
            $('#group_sender_id').val(senderId);
            $('#group_sender_name').val(senderName);
            $('#group_id').val(groupId);
            $('#group_name').val(groupName);
            $('#groupMessageModal').modal('show');
        }
        
        // Function to show message form for all users
        function showAllUserMessageForm(senderId, senderName) {
            $('#user_sender_id').val(senderId);
            $('#user_sender_name').val(senderName);
            $('#allUserMessageModal').modal('show');
        }
        
        // Function to update notification count
        function updateNotificationCount() {
            $.ajax({
                url: "{{ route('update.notification.count', ['userId' => auth()->id()]) }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    $('#notification-count').text('');
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
        }
        
        // Function to send message to a single user
        function sendMessageToSingleUser() {
            let receiverId = $('#receiver_id').val();
            let message = $('#message').val();
            let senderId = $('#sender_id').val();
            let senderName = $('#sender_name').val()
            let messageType = "individual";
            $.ajax({
                url: "{{ route('send.message.to.single.user') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    receiver_id: receiverId,
                    content: message,
                    sender_id: senderId,
                    sender_name: senderName,
                    message_type: messageType,
                },
                success: function(response) {
                    $('#messageModal').modal('hide');
                    toastr.success('Message Sent Succesfully!!')
                    $('#message').val('');
                },
                error: function(xhr, status, error) {
                    toastr.error('Oops Something Went Wrong!!')

                }
            });
        }
        
        // Function to send message to a group
        function sendMessageToGroup() {
            let message = $('#group_message').val();
            let senderId = $('#group_sender_id').val();
            let senderName = $('#group_sender_name').val();
            let groupId = $('#group_id').val();
            let groupName = $('#group_name').val();
            let messageType = "group";
            $.ajax({
                url: "{{ route('send.message.to.group') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    content: message,
                    sender_id: senderId,
                    sender_name: senderName,
                    group_id: groupId,
                    message_type: messageType,
                    group_name: groupName,
                },
                success: function(response) {
                    $('#groupMessageModal').modal('hide');
                    toastr.success('Message Sent Succesfully!!')
                    $('#group_message').val('');
                },
                error: function(xhr, status, error) {
                    toastr.error('Oops Something Went Wrong!!')

                }
            });
        }
        
        // Function to send message to all users
        function sendMessageToAllUsers() {

            let message = $('#alluser_message').val();
            let senderId = $('#user_sender_id').val();
            let senderName = $('#user_sender_name').val();
            let messageType = "all";
            $.ajax({
                url: "{{ route('send.message.to.all') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    content: message,
                    sender_id: senderId,
                    sender_name: senderName,
                    message_type: messageType,
                },
                success: function(response) {
                    $('#allUserMessageModal').modal('hide');
                    toastr.success('Message Sent Succesfully!!')
                    $('#alluser_message').val('');
                },
                error: function(xhr, status, error) {
                    toastr.error('Oops Something Went Wrong!!')

                }
            });
        }

        $(document).ready(function() {
            $('#usersTable').DataTable({
                "paging": true,
                "searching": true,

            });
            $('#groupsTable').DataTable({
                "paging": true,
                "searching": true,

            });
            $('#messageForm').submit(function(e) {
                e.preventDefault();
                sendMessageToSingleUser();
            });
            $('#groupMessageForm').submit(function(e) {
                e.preventDefault();
                sendMessageToGroup();
            });
            $('#allUserMessageForm').submit(function(e) {
                e.preventDefault();
                sendMessageToAllUsers();
            });
            
            // AJAX request to get message list and update notifications
            $.ajax({
                url: "{{ route('get.message.list', ['userId' => auth()->id()]) }}",
                type: "GET",
                success: function(data) {
                    let notificationCount = data.notificationCount;
                    $('#notification-count').text(notificationCount).toggle(notificationCount > 0);
                    let notificationList = $('#notification-list');
                    data.messages.forEach(function(message) {
                        let notificationText = '';
                        if (message.type === 'all') {
                            notificationText += '<strong>All: ' + message.sender_name +
                                ':</strong> ' + message.content;
                        } else {
                            notificationText += '<strong>' + (message.group_name ? message
                                    .group_name + ': ' : '') + message.sender_name +
                                ':</strong> ' + message.content;
                        }
                        notificationList.append('<li class="dropdown-item">' +
                            notificationText + '</li>');
                    });
                    if (data.messages.length > 0) {

                        notificationList.find('li').filter(function() {
                            return $(this).text().trim() === 'No new notifications';
                        }).remove();
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                }
            });
            $('.subscribe-btn').click(function() {
                let userId = $(this).data('user-id');
                let groupId = $(this).data('group-id');
                let button = $(this);

                $.ajax({
                    url: "{{ route('subscribe') }}",
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                        user_id: userId,
                        group_id: groupId
                    },
                    success: function(response) {
                        if (response.is_subscribed) {
                            button.text('UnSubscribe').addClass('btn-danger');
                            button.closest('tr').find('.send-btn[data-group-id="' + groupId +
                                '"]').prop('disabled', false);
                            window.Echo.private('group.' + groupId)
                                .listen('GroupMessageEvent', (e) => {
                                    handleNotification(e, notificationCount,
                                        notificationList);
                                });
                        } else {

                            button.text('Subscribe').removeClass('btn-danger');
                            button.closest('tr').find('.send-btn[data-group-id="' + groupId +
                                '"]').prop('disabled', true);
                            window.Echo.leave('group.' + groupId);



                        }
                    }
                });
            });
            
            // Event listeners for WebSocket notifications
            let userId = {{ auth()->id() }};
            let notificationCount = $('#notification-count');
            let notificationList = $('#notification-list');
            
            // Function to handle incoming notifications
            function handleNotification(e, notificationCount, notificationList) {
                if (e.messageData['sender_id'] == userId) {
                    return;
                }
                let encryptedMessage = e.messageData['content'];
                let groupName = e.messageData['message_type'] === "group" ? e.messageData['group_name'] + ': ' : '';
                let all = e.messageData['message_type'] === 'all' ? 'All' + ': ' : '';

                $.ajax({
                    url: "{{ route('decrypt.message') }}",
                    type: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        encryptedMessage: encryptedMessage,
                    },
                    success: function(response) {
                        let decryptedMessage = response.decryptedMessage;
                        let count = parseInt(notificationCount.text().trim()) || 0;

                        notificationCount.text(count + 1).show();

                        let notificationItem = $('<li class="dropdown-item"></li>').html(
                            `<strong>${groupName}${all}${e.messageData['sender_name']}:</strong> ${decryptedMessage}`
                        );

                        notificationList.find('.dropdown-header').after(notificationItem);
                        notificationList.find('li').each(function() {
                            if ($(this).text().trim() === 'No new notifications') {
                                $(this).remove();
                            }
                        });
                        

                    },
                    error: function(xhr, status, error) {
                        console.error(error);
                    }
                });
            }

            // Listen for WebSocket events
            window.Echo.private('user.' + userId)
                .listen('OneToOneMessageEvent', (e) => {
                    handleNotification(e, notificationCount, notificationList);
                });
            subscribedGroupIds.forEach(groupId => {
                window.Echo.private('group.' + groupId)
                    .listen('GroupMessageEvent', (e) => {
                        handleNotification(e, notificationCount,
                            notificationList);
                    });
            });
            Echo.channel('allUser')
                .listen('AllUsersMessageEvent', (e) => {
                    handleNotification(e, notificationCount, notificationList);
                });

        });
    </script>
</body>
</html>
