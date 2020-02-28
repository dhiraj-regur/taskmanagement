<?php

interface Isd_Role
{
    const GUEST   = 'guest';
    const USER    = 'user';
    const PREMIUM = 'premium';
    const EDITOR  = 'editor';
    const ADMIN   = 'admin';

    public function isGuest();
    public function isUser();
    public function isPremium();
    public function isEditor();
    public function isAdmin();
}