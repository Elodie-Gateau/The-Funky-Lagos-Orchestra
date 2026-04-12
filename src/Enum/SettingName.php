<?php
// src/Enum/SettingName.php
namespace App\Enum;

enum SettingName: string
{
    case Photo = 'Photo';
    case Description = 'Description';
    case Email = 'Email';
    case Phone = 'Phone';
    case Facebook = 'Facebook';
    case Instagram = 'Instagram';
    case YouTube = 'YouTube';
    case SoundCloud = 'SoundCloud';
}
