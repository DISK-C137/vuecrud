<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VueJS CRUD APP whith PHP</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>

    <div class="container mt-5" id="crudApp">
        <br>
        <h3 align="center">CRUD APP using VUEJS & PHP</h3>
        <hr>
        <br>
        <div class="row">
            <div class="col-md-6">
                <h3 class="panel-title">Users Data</h3>
            </div>
            <div class="col-md-6" align="right">
                <input type="button" class="btn btn-success btn-xs" data-bs-toggle="modal" data-bs-target="#myModal" @click="openModal" value="Add" />
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <tr>
                    <th>First Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
                <tr v-for="row in allData">
                    <td>{{row.first_name}}</td>
                    <td>{{row.last_name}}</td>
                    <td>{{row.email}}</td>
                    <td>
                        <button type="button" name="edit" class="btn btn-primary btn-xs edit" data-bs-toggle="modal" data-bs-target="#myModal" @click="fetchData(row.id)">Edit</button>
                        <button type="button" name="delete" class="btn btn-danger btn-xs delete" data-bs-toggle="modal" data-bs-target="#myModal" @click="deleteData(row.id)">Delete</button>
                    </td>
                </tr>
            </table>
        </div>
        <br>
        <div v-if="myModal" class="modal fade" id="myModal" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">{{dynamicTitle}}</h4>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" @click="myModal=false"></button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="firstname">First Name</label>
                            <input type="text" v-model="first_name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="lastname">Last Name</label>
                            <input type="text" v-model="last_name" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" v-model="email" class="form-control">
                        </div>
                        <br>
                        <div class="modal-footer">
                            <input type="hidden" v-model="hiddenId">
                            <input type="button" v-model="actionButton" @click="submitData" class="btn btn-success btn-xs">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>

    <script>
        let app = new Vue({
            el: '#crudApp',
            data: {
                allData: '',
                myModal: false,
                hiddenId: null,
                actionButton: 'Insert',
                dynamicTitle: 'Add data'
            },
            methods: {
                fetchAllData() {
                    axios.post('action.php', {
                        action: 'fetchall'
                    }).then(res => {
                        app.allData = res.data;
                    });
                },
                openModal() {
                    app.first_name = '';
                    app.last_name = '';
                    app.email = '';
                    app.actionButton = 'Insert';
                    app.dynamicTitle = 'Add Data';
                    app.myModal = true;
                },
                submitData() {
                    if (app.first_name != '' && app.last_name != '' && app.email != '') {
                        //console.log(app.first_name+''+app.last_name+''+app.email)
                        if (app.actionButton == 'Insert') {
                            axios.post('action.php', {
                                action: 'insert',
                                firstName: app.first_name,
                                lastName: app.last_name,
                                email: app.email
                            }).then(res => {
                                app.myModal = false;
                                app.fetchAllData();
                                app.first_name = '';
                                app.last_name = '';
                                app.email = '';
                                alert(res.data.message);
                                window.location.reload();
                            });
                        }
                    }
                    if (app.actionButton === 'Update') {
                        axios.post('action.php', {
                            action: 'update',
                            firstName: app.first_name,
                            lastName: app.last_name,
                            email: app.email,
                            hiddenId: app.hiddenId
                        }).then(res => {
                            app.myModal = false;
                            app.fetchAllData();
                            app.first_name = '';
                            app.last_name = '';
                            app.email = '';
                            app.hiddenId = '';
                            alert(res.data.message);
                            window.location.reload();
                        })
                    }
                },
                fetchData(id) {
                    axios.post('action.php', {
                        action: 'fetchSingle',
                        id: id
                    }).then(res => {
                        app.first_name = res.data.first_name;
                        app.last_name = res.data.last_name;
                        app.email = res.data.email;
                        app.hiddenId = res.data.id;
                        app.myModal = true;
                        app.actionButton = "Update";
                        app.dynamicTitle = "Edit Data";
                    })
                },
                deleteData(id) {
                    if (confirm("Are you sure you want to remove this data?")) {
                        axios.post('action.php', {
                            action: 'delete',
                            id: id
                        }).then(res => {
                            app.fetchAllData();
                            alert(res.data.message);
                        });
                    }
                }
            },
            created() {
                this.fetchAllData();
            }
        })
    </script>
</body>

</html>