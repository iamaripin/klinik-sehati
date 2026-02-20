<?php

namespace App\Livewire\MasterData;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use App\Models\User as UserModel;

class User extends Component
{
    public $users = [];

    public $id;
    public $name;
    public $email;
    public $password;
    public $username;
    public $title;
    public $suffix;
    public $user_dob;
    public $gender;
    public $role;

    public $isEdit = false;
    protected $listeners = ['openCreateModal', 'openEditModal'];

    public function mount()
    {
       return $this->loadUsers();
    }

    public function render()
    {
        return view('livewire.master-data.user-data', [
            'data' => $this->users,
        ])->layout('layouts.app', [
            'title' => 'Data User',
        ]);
    }


    private function loadUsers()
    {
        $this->users = UserModel::latest()->get();
    }

    public function openModal()
    {
        $this->resetForm();
        $this->dispatch('show-form');
    }

    public function openCreateModal()
    {
        $this->resetForm();
        $this->dispatch('show-form');
    }

    public function openEditModal($id)
    {
        $this->resetForm();
        $this->isEdit = true;

        $user = UserModel::findOrFail($id);

        $this->id        = $user->id;
        $this->name      = $user->name;
        $this->email     = $user->email;
        $this->username  = $user->username;
        $this->title     = $user->title;
        $this->suffix    = $user->suffix;
        $this->user_dob  = optional($user->user_dob)->format('Y-m-d');
        $this->gender    = $user->gender;
        $this->role      = $user->role;

        $this->dispatch('show-form');
    }

    public function save()
    {
        $rules = [
            'name' => 'required',
            'role' => 'required',
        ];

        if ($this->isEdit) {
            $rules['email'] = 'required|email|unique:users,email,' . $this->id;
            $rules['username'] = 'required|unique:users,username,' . $this->id;

            if ($this->password) {
                $rules['password'] = 'min:2';
            }
        } else {
            $rules['email'] = 'required|email|unique:users,email';
            $rules['username'] = 'required|unique:users,username';
            $rules['password'] = 'required|min:2';
        }

        $this->validate($rules);

        if ($this->isEdit) {
            $user = UserModel::findOrFail($this->id);

            $updateData = [
                'name'      => $this->name,
                'email'     => $this->email,
                'username'  => $this->username,
                'title'     => $this->title,
                'suffix'    => $this->suffix,
                'user_dob'  => $this->user_dob,
                'gender'    => $this->gender,
                'role'      => $this->role,
            ];

            if ($this->password) {
                $updateData['password'] = $this->password;
            }

            $user->update($updateData);

            $this->dispatch('toastr:info', message: 'User berhasil diperbarui!');
        } else {

            UserModel::create([
                'name'      => $this->name,
                'email'     => $this->email,
                'password'  => $this->password,
                'username'  => $this->username,
                'title'     => $this->title,
                'suffix'    => $this->suffix,
                'user_dob'  => $this->user_dob,
                'gender'    => $this->gender,
                'role'      => $this->role,
                'created_at' => now(),
            ]);

            $this->dispatch('toastr:success', message: 'User berhasil ditambahkan!');
        }

        $this->loadUsers();           // refresh data
        $this->resetForm();
        $this->dispatch('hide-form'); // tutup modal
        $this->dispatch('refresh-table'); // refresh datatable
    }

    public function userConfirm($id)
    {
        $this->id = $id;
        $this->dispatch('confirm-update-user');
    }

    public function updateUserStatus()
    {
        $user = UserModel::findOrFail($this->id);

        if ($user->status == 'non active') {
            $updateData = [
                'status'      => 'active',
            ]; 
        } else {
            $updateData = [
                'status'      => 'non active',
            ];
        }
 
        $user->update($updateData);
        $this->loadUsers();

        $this->dispatch('toastr:error', message: 'Data berhasil diUpdate!');
        $this->dispatch('refresh-table');
    }

    protected function resetForm()
    {
        $this->reset([
            'id',
            'name',
            'email',
            'password',
            'username',
            'title',
            'suffix',
            'user_dob',
            'gender',
            'role',
        ]);

        $this->isEdit = false;
    }
}
