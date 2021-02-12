<template>
    <div class="box-body">
        <div class="row text-right" v-if="!createMode">
            <div class="col-9">&nbsp;</div>
            <div class="col-3">
                <button type="button" class="btn btn-primary" @click="addNew">Add New</button>
                <button type="button" class="btn btn-danger" @click="bulkDestroy">Bulk Delete</button>
            </div>
        </div>
        <table class="table table-bordered mt-2" v-if="!createMode">
            <thead>
                <tr>
                    <th>Srno</th>
                    <th>Select</th>
                    <th>Name</th>
                    <th>Contact No</th>
                    <th>Hobby</th>
                    <th>Category</th>
                    <th>Profile Pic</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <tr v-if="!isLoading && users.length < 1">
                    <td colspan="8" class="text-center">
                        Data not found.
                    </td>
                </tr>
                <tr v-else v-for="(user, index) in users" :key="user.id" :class="{editing: user == editedUser}" v-cloak>
                    <td>{{ getNumber(index) }}</td>
                    <td>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" v-model="deleteUsers" :value="user.id">
                        </div>
                    </td>
                    <td>
                        <div class="view">
                            {{user.name}}
                        </div>
                        <div class="edit">
                            <input type="text" class="form-control" v-model="user.name"/>
                        </div>
                    </td>
                    <td>
                        <div class="view">
                            {{user.contact_number}}
                        </div>
                        <div class="edit">
                            <input type="text" class="form-control" v-model="user.contact_number"/>
                        </div>
                    </td>
                    <td>
                        <div class="view">
                            {{ drawHobbies(user.hobbies) }}
                        </div>
                        <div class="edit">
                            <div v-for="hobby in hobbies" :key="hobby.id" class="form-check">
                                <input class="form-check-input" type="checkbox" v-model="user.hobby_ids" :value="hobby.id">
                                <label class="form-check-label" for="gridCheck1">
                                    {{ hobby.name }}
                                </label>
                            </div>
                        </div>
                    </td>
                    <td>
                        <div class="view">
                            {{ user.category.name }}
                        </div>
                        <div class="edit">
                            <select class="form-control" v-model="user.category_id">
                                <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                            </select>
                        </div>
                    </td>
                    <td>
                        <div class="view">
                            <img :src="`/photos/${user.photo}`" width="75px" />
                        </div>
                        <div class="edit">
                            <input type="file" class="form-control-file" accept="image/*" :id="'edit-file-' + user.id" />
                            <sup>*</sup>If not upload will use old photo
                        </div>
                    </td>
                    <td>
                        <div class="view">
                            <button class="btn btn-success" @click="editData(user)">Edit</button>
                        </div>
                        <div class="edit">
                            <button class="btn btn-success" @click="updateData(user)">Save</button>
                        </div>
                        <button type="button" class="btn btn-danger" @click="destroy(user.id)">Delete</button>
                    </td>
                </tr>
                <tr v-if="isLoading">
                    <td colspan="8" class="text-center">
                        <pulse-loader></pulse-loader>
                    </td>
                </tr>
            </tbody>
        </table>
        <b-pagination
            v-model="currentPage"
            :total-rows="totalData"
            :per-page="perPage"
            @change="getData"
            aria-controls="my-table"
            v-if="!createMode"
        >
        </b-pagination>
        <form v-if="createMode">
             <div class="form-group row">
                <label for="name" class="col-sm-2 col-form-label">Name</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" v-model="form.name">
                </div>
            </div>
            <div class="form-group row">
                <label for="contact_number" class="col-sm-2 col-form-label">Contact No</label>
                <div class="col-sm-10">
                    <input type="text" class="form-control" v-model="form.contact_number">
                </div>
            </div>
            <div class="form-group row">
                <label for="hobby" class="col-sm-2 col-form-label">Hobby</label>
                <div class="col-sm-10">
                    <div v-for="hobby in hobbies" :key="hobby.id" class="form-check">
                        <input class="form-check-input" type="checkbox" v-model="form.hobbies" :value="hobby.id">
                        <label class="form-check-label" for="gridCheck1">
                            {{ hobby.name }}
                        </label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="category_id" class="col-sm-2 col-form-label">Category</label>
                <div class="col-sm-10">
                    <select class="form-control" v-model="form.category_id">
                        <option value="">Select Category</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                    </select>
                </div>
            </div>
            <div class="form-group row">
                <label for="contact_number" class="col-sm-2 col-form-label">Profil Pic</label>
                <div class="col-sm-10">
                    <input type="file" class="form-control-file" accept="image/*" id="file" />
                </div>
            </div>
            <button type="button" class="btn btn-primary" @click="store">Save</button>
            <button type="button" class="btn btn-secondary" @click="cancelCreate">Cancel</button>
        </form>
    </div>
</template>
<style scoped>
    [v-cloak] {
      display: none;
    }
    .edit {
      display: none;
    }
    .editing .edit {
      display: block
    }
    .editing .view {
      display: none;
    }
</style>
<script>  
    import axios from 'axios';
    import PulseLoader from 'vue-spinner/src/PulseLoader.vue'

    export default {
        data() {
            return {
                editMode: false,
                createMode: false,
                currentPage: 1,
                totalData: 0,
                perPage: 15,
                users: [],
                categories: [],
                hobbies: [],
                isLoading: true,
                form: this.resetForm(),
                deleteUsers: [],
                editedUser: null,
            }
        },
        components: {
            PulseLoader
        },
        methods: {
            checkBoxes(hobbies, value) {
                for (let i = 0;i < hobbies.length; i++) {
                    if (hobbies[i] === value) {
                        return true;
                    }
                }

                return true;
            },
            resetForm() {
                return {
                    name: '',
                    contact_number: '',
                    hobbies: [],
                    category_id: '',
                    photo: ''
                };
            },
            getNumber(index) {
                return ((this.currentPage - 1) * this.perPage) + index + 1;
            },
            addNew() {
                this.form = this.resetForm()
                this.createMode = true;
            },
            cancelCreate() {
                this.createMode = false;
            },
            editData (user) {
                this.beforEditCache = user;
                this.editedUser = user;
            },
            updateData(user) {
                console.log(user);
                const vm = this;
                let formData = new FormData();

                var imagefile = document.querySelector('#edit-file-' + user.id);

                formData.append('name', user.name);
                formData.append('contact_number', user.contact_number);
                let index = 0;
                user.hobby_ids.forEach((hobby) => {
                    formData.append(`hobbies[${index++}]`, hobby);
                });
                formData.append('category_id', user.category_id);
                if (imagefile && imagefile.files[0]) {
                    formData.append('photo', imagefile.files[0]);
                } else {
                    formData.append('photo', null);
                }

                axios.post(`user/${user.id}/update`,
                    formData, 
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                    ).then(function (res) {
                        vm.createMode = false;
                        vm.$swal.fire(
                            'Information',
                            res.data.message,
                            'success'
                        );
                        vm.getData();
                    })
                    .catch(function (err) {
                        vm.drawErrors(err.response.data.errors);
                    });
            },
            getData(page = 1) {
                const vm = this;

                axios.get(`/user/list?page=${page}`)
                    .then((res) => {
                        vm.users = res.data.data;
                        vm.currentPage = res.data.current_page;
                        vm.totalData = res.data.total;
                        vm.perPage = res.data.per_page;
                    }).catch((err) => {
                        console.log(err);
                    }).then(function () {
                        vm.isLoading = false;
                    }); 
            },
            getCategories() {
                const vm = this;

                axios.get(`/user/categories`)
                    .then((res) => {
                        vm.categories = res.data.data;
                    }).catch((err) => {
                        console.log(err);
                    });
            },
            getHobbies() {
                const vm = this;

                axios.get(`/user/hobbies`)
                    .then((res) => {
                        vm.hobbies = res.data.data;
                    }).catch((err) => {
                        console.log(err);
                    });
            },
            drawHobbies(hobbies) {
                let res = [];
                hobbies.forEach((hobby) => {
                    res.push(hobby.hobby.name);
                });

                return res.join(', ');
            },
            store() {
                const vm = this;
                let formData = new FormData();

                var imagefile = document.querySelector('#file');

                formData.append('name', vm.form.name);
                formData.append('contact_number', vm.form.contact_number);
                let index = 0;
                vm.form.hobbies.forEach((hobby) => {
                    formData.append(`hobbies[${index++}]`, hobby);
                });
                formData.append('category_id', vm.form.category_id);
                if (imagefile) {
                    formData.append('photo', imagefile.files[0]);
                } else {
                    formData.append('photo', null);
                }

                axios.post('user/store',
                    formData, 
                    {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    }
                    ).then(function (res) {
                        vm.createMode = false;
                        vm.$swal.fire(
                            'Information',
                            res.data.message,
                            'success'
                        );
                        vm.getData();
                    })
                    .catch(function (err) {
                        vm.drawErrors(err.response.data.errors);
                    });
            },
            drawErrors(errors) {
                let errorMessage = '';
                for (let error in errors) {
                    for (let i=0;i < errors[error].length;i++) {
                        errorMessage += errors[error][i];
                        errorMessage += '<br />';
                    }
                }

                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    html: errorMessage,
                });
            },
            destroy(id) {
                const vm = this;

                vm.$swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.delete(`user/${id}/destroy`)
                        .then(function (res) {
                            vm.$swal.fire(
                                'Deleted!',
                                res.data.message,
                                'success'
                            );

                            vm.getData();
                        })
                        .catch(function (err) {
                            console.log(err);
                        });
                        
                    }
                });
            },

            bulkDestroy() {
                const vm = this;

                if (vm.deleteUsers.length < 1) {
                    vm.$swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Checkbox must be selected',
                    });

                    return;
                }

                vm.$swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        axios.delete(`user/bulk-destroy`, {
                            params: {
                                ids: vm.deleteUsers
                            }
                        })
                        .then(function (res) {
                            vm.$swal.fire(
                                'Deleted!',
                                res.data.message,
                                'success'
                            );

                            vm.getData();
                        })
                        .catch(function (err) {
                            vm.drawErrors(err.response.data.errors);
                        });
                        
                    }
                });
            },
        },
        mounted() {
            this.getData();
            this.getHobbies();
            this.getCategories();
        }
    }
</script>
