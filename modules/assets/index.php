<?php

$pageTitle = "Inventar";

include "../../layouts/admin_layout_start.php";

require_once $_SERVER['DOCUMENT_ROOT']
    . '/anketa/config/init.php';
require_once 'helpers/permissions.php';
require_once 'helpers/audit.php';

require_Login();

if (!hasRole(['admin', 'it'])) {
    die('Nemate dozvolu.');
} ?>

<li class="sidebar-item has-submenu">

    <a
        href="#"
        class="sidebar-link">

        <i class="fa-solid fa-computer"></i>

        <span>

            Inventar

        </span>

        <i class="fa-solid fa-chevron-down submenu-arrow"></i>

    </a>

    <ul class="sidebar-submenu">

        <li>

            <a
                href="/anketa/modules/assets/index.php">

                <i class="fa-solid fa-table-columns"></i>

                Pregled inventara

            </a>

        </li>

        <li>

            <a
                href="/anketa/modules/assets/assets/index.php">

                <i class="fa-solid fa-laptop"></i>

                IT Inventar

            </a>

        </li>

        <li>

            <a
                href="/anketa/modules/assets/assignments/index.php">

                <i class="fa-solid fa-user-check"></i>

                Zaduženja

            </a>

        </li>

        <li>

            <a
                href="/anketa/modules/assets/history/index.php">

                <i class="fa-solid fa-clock-rotate-left"></i>

                Istorija promena

            </a>

        </li>

        <li class="submenu-divider">

            <span>

                Administracija

            </span>

        </li>

        <li>

            <a
                href="/anketa/modules/assets/types/index.php">

                <i class="fa-solid fa-layer-group"></i>

                Tipovi inventara

            </a>

        </li>

        <li>

            <a
                href="/anketa/modules/assets/categories/index.php">

                <i class="fa-solid fa-folder-tree"></i>

                Kategorije

            </a>

        </li>

        <li>

            <a
                href="/anketa/modules/assets/attributes/definitions.php">

                <i class="fa-solid fa-list-check"></i>

                Atributi

            </a>

        </li>

    </ul>

</li>