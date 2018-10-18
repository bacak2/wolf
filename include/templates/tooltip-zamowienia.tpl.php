<p style='text-align: right; font-size: 12px;'>
    <span class="tooltip-header">Użytkownik</span><br />
    <?php echo $Element['client_name']; ?>
    <br>
    <?php echo $Element['role_name']; ?>
    <br />
    <span class="tooltip-header">Telefon</span><br />
    <?php echo $Element['phone']; ?>
    <br />
    <span class="tooltip-header">Firma</span><br />
    <a href="?modul=firma&akcja=szczegoly&id=<?php echo $Element['company_id']; ?>"><?php echo $Element['simplename']['simplename']; ?></a>
</p>
