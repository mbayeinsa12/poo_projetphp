<?php

class Role {
    private $id;
    private $nom;
    private $permissions = [];

    public function __construct($id, $nom, array $permissions = []) {
        $this->id = $id;
        $this->nom = $nom;
        $this->permissions = $permissions;
    }

    public function getId() { return $this->id; }
    public function getNom() { return $this->nom; }
    public function getPermissions() { return $this->permissions; }

    public function hasPermission($permission) {
        return in_array($permission, $this->permissions);
    }

    // Méthodes pour ajouter et supprimer des permissions
    public function addPermission($permission) {
        if (!in_array($permission, $this->permissions)) {
            $this->permissions[] = $permission;
        }
    }

    public function removePermission($permission) {
        $this->permissions = array_diff($this->permissions, [$permission]);
    }
}

?>