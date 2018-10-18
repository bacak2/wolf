function getRandomString (length, level) {
    chars = [];
    chars['letters'] = 'qwertyuiopasdfghjklzxcvbnm';
    chars['lettersBig'] = 'QWERTYUIOPASDFGHJKLZXCVBNM';
    chars['digits'] = '1234567890';
    chars['special'] = '!@#$%^&*()_+-=[]{};:,.<>?';

    charsString = '';

    if (level > 0)    charsString += chars['letters'];
    if (level > 1) charsString += chars['lettersBig'];
    if (level > 2)     charsString += chars['digits'];
    if (level > 3)    charsString += chars['special'];

	string = '';

	for (i=0; i < length; i++) {
		charNum = Math.floor( Math.random() * charsString.length );
		string += charsString.charAt(charNum);
	}

    return string;
}

function genrePass(length, level) {
    var haslo = getRandomString(length, level);
    $('#haslo').val(haslo);
    $('#reply_haslo').val(haslo);
    $("#wygenerowane-haslo").text(haslo).html();
}