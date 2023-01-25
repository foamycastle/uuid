<?php

namespace FoamyCastle\UUID;

enum UUIDVersion:int {
    case VERSION_1=1; //time-based, clock sequence, node ID (random or set)
    case VERSION_3=3; //namespaced (random or set), md5 hash
    case VERSION_4=4; //completely random
    case VERSION_5=5; //namespaced (random or set), sha1 hash
}
