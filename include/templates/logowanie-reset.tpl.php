<!-- Formularz logowania { -->
<table id="login">
    <thead>
	<tr>
            <td class="top">
                <img src="images/wolf_logo.gif" alt="" border="0" style="margin-left: 5px;" />
                <img src="images/logo.gif" alt="" border="0" style="margin-left: 5px;" />
            </td>
	</tr>
    </thead>
    <tbody>
    <tr>
            <td class="middle">
            <br />
                <?php
                        if(isset($Komunikat) && !empty($Komunikat)){
                        ?>
                                <?php 
                                call_user_func( 'Usefull::'.$Komunikat['func'], $Komunikat['tresc']);
                                ?>
                        <?php
                        }
                        ?>
                    <form action="./?resetpassword=<?php echo $_GET['resetpassword'];?>" method="post">
                    <table style="width: 240px;margin: 0 auto;">
                        <tr>
                            <td colspan="3">
                                <h3 style="text-align: center;">Zmiana hasła</h3>
                                <input type="hidden" value="<?php echo $token; ?>" name="token">
                            </td>
                        </tr>
                            <tr>
                                    <td align="right" valign="middle"><p class="logowanie">Nowe hasło</td>
                                    <td colspan="2"><input type="text" name="pp_haslo" size="20" border="0" class="tabelka"></td>
                            </tr>
                            <tr>
                                    <td colspan="3" align="right" valign="middle">
                                        <input type="hidden" name="zmien" value="yes" />
                                        <input type="submit" value='Zmień Hasło' class='zaloguj'>
                                    </td>
                            </tr>
                            <tr>
                                <td colspan="3" align="center" valign="middle">
                                    <a style="font-size:10px;color:#777" href="./">logowanie</a>
                                </td>
                            </tr>
                    </table>
                    </form>
            </td>
    </tr>
    <tr>
            <td><img src="images/dol_logowanie.gif" alt="" height="26" width="480" border="0"></td>
    </tr>
    <tr>
            <td><p class="logowanie_dol">copyright  2004-<?php echo date("Y"); ?> <a href="http://www.artplus.pl" target="_blank" class="log">ARTplus</a></td>
    </tr>
</table>
<!-- } Formularz logowania -->
