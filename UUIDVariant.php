<?php

namespace FoamyCastle\UUID;

enum UUIDVariant:int {
	case VARIANT_NCS=0;
	case VARIANT_RFC4122=4;
	case VARIANT_MICROSOFT=6;
	case VARIANT_FUTURE_DEF=7;
}
