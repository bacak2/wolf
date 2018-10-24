<!-- IE ERROR SSL -->
<?php
if(preg_match('/(?i)msie [1-8]\./',$_SERVER['HTTP_USER_AGENT'])  )
{ ?>
<div class="komunikat_ostrzezenie" style="margin: 0 auto;">
Uwaga! W przypadku przeglądarki Internet Eksplorer w wersji 8 i
wcześniejszych w systemie Windows XP występuje znany problem z poprawną
weryfikacją certyfikatu SSL. Zalecane jest skorzystanie z przeglądarki
FireFox, Opera lub Chrome.
</div>

<?php } ?>

<!-- Formularz logowania { -->
<table id="login">
    <thead>
	<tr>
		<td class="top" style="text-align:right;">
			<div style="float:right;">
				<div style="margin: 8px 0px;width: 250px;text-align:center;"><img src="images/wolf_logo.png" alt="" border="0" /></div>
				<div style="margin: 8px 0px;width: 250px;text-align:center;"><img src="images/serwisplus.png" alt="" border="0" /></div>
			</div>
		</td>											
	</tr>
    </thead>
    <tbody>
	<tr>
            <td class="middle">
                <br />
                <form action="./" method="post">
                <input type="hidden" name="logowanie" value="1">
                <table style="width: 240px;margin: 0 auto;">
                    <tr>
                        <td align="right" valign="middle"><p class="logowanie">Użytkownik</td>
                        <td colspan="2"><input type="text" name="pp_login" size="20" border="0" class="tabelka"></td>
                    </tr>
                    <tr height="1">
                            <td colspan="3" height="1"></td>
                    </tr>
                    <tr>
                        <td align="right" valign="middle"><p class="logowanie">Hasło</td>
                        <td colspan="2"><input type="password" name="pp_haslo" size="20" border="0" class="tabelka"></td>
                    </tr>
                    <tr>
                        <td colspan="3" align="right" valign="middle"><input type="hidden" name="logowanie" value="yes" />
                            <input type="submit" value='Zaloguj' class='zaloguj'>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="3" align="center" valign="middle">
                            <a style="font-size:10px;color:#777" href="?haslo=new">Przypomnij hasło</a>
                        </td>
                    </tr>
                </table>
                </form>
            </td>
	</tr>
	<tr>
		<td class="login_bottom"></td>
	</tr>
	<tr>
		<td><p class="logowanie_dol">powered by <a href="http://www.artplus.pl" target="_blank" class="log">ARTplus</a></td>
	</tr>
    </tbody>
</table>
<!-- } Formularz logowania -->
