{assign var='dark' value='#333'}
{assign var='blue' value='#1e4688'}
{assign var='red' value='#d5121d'}
{assign var='grey' value='#f2f2f2'}
{assign var='darkGrey' value='lightgrey'}
{assign var='red' value='#d5121d'}


<style>

	div.product-name {
		background-color: {$blue};
		line-height: 40px;
		font-size: 20px;
		font-weight: bold;
		text-align: center;
		color: white;
	}
	div.page-break {
		page-break-after: always;
	}

	div.footer {
		text-align: center;
		font-style: italic;
		font-size: 10px;
		color: grey;
	}

	table { 
		width: 100%; 
	}

	table.left-title th {
		background-color: {$dark};
		color: white;
		font-weight: bold;
		font-size: 14px;
		text-align: center;
		padding: 10px;
	}
	table.left-title tr>td.title {
		background-color: {$darkGrey};
		font-weight: bold;
		text-align: left;
	}
	table.left-title tr>td {
		background-color: {$grey};
		text-align: right;
	}

	table.product td.image {
		text-align: center;
	}

	table.combinations th {
		background-color: {$dark};
		color: white;
		font-weight: bold;
		font-size: 14px;
		text-align: center;
	}

	table.combinations tr.odd {
		background-color: {$grey};
	}
	table.combinations tr.even {
		background-color: {$darkGrey};
	}

	.check {
		width: 20px;
		height: 20px;
		border: 2px solid {$dark};
		border-radius: 4px;
		background-color: white;
		margin-top: 5px;
	}

	.block {
		border:2px solid black;
	}
	
	.text-center { text-align: center; }
	.text-right { text-align: right; }

	.text-primary { color: {$blue}; }
	.text-danger { color: {$red}; }
	.text-light { color: white; }

	.bg-primary { background-color: {$blue}; color: white;}
	.bg-light { background-color: {$grey}; }
	.bg-grey { background-color: {$darkGrey}; }

	.bold { font-weight: bold; }

</style>