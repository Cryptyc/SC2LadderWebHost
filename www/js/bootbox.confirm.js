var SavedBotId = 0;

function DeleteConfirm(BotId)
{
	SavedBotId = BotId;
	bootbox.confirm({
		message: "Really delete this bot?",
		buttons: {
			confirm: {
				label: 'Yes',
				className: 'btn-danger'
			},
			cancel: {
				label: 'No',
				className: 'btn-success'
			}
		},
		callback: function (result) {
			if(result)
			{
				
				console.log('Bot delete confirmed');
				window.location.href = 'Bots.php?DeleteBotId=' + SavedBotId;
			}
		}
	});
}

function DeleteTournConfirm(BotId)
{
	SavedBotId = BotId;
	bootbox.confirm({
		message: "Really delete this bot?",
		buttons: {
			confirm: {
				label: 'Yes',
				className: 'btn-danger'
			},
			cancel: {
				label: 'No',
				className: 'btn-success'
			}
		},
		callback: function (xresult) {
			if(xresult)
			{
				
				console.log('Bot delete confirmed');
				window.location.href = 'TournamentBots.php?DeleteBotId=' + SavedBotId;
			}
		}
	});
}