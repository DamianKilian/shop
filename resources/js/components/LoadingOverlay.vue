<template>
    <div
        style="
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: #ccc;
            top: 0;
            right: 0;
            z-index: 2000;
            opacity: 0.5;
        "
    >
        <div
            class="position-absolute top-50 start-50 translate-middle spinner-grow"
            role="status"
        >
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</template>
