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

</style>