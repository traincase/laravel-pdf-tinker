<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>A simple, clean, and responsive HTML invoice template</title>

    <style>
        .invoice-box {
            max-width: 800px;
            margin: auto;
            padding: 30px;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, .15);
            font-size: 16px;
            line-height: 24px;
            font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
            color: #555;
        }

        .invoice-box table {
            width: 100%;
            text-align: left;
        }

        .invoice-box table td {
            padding: 5px;
            vertical-align: top;
        }

        .invoice-box table tr td:nth-child(2) {
            text-align: right;
        }

        .invoice-box table tr.top table td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.top table td.title {
            font-size: 45px;
            line-height: 45px;
            color: #333;
        }

        .invoice-box table tr.information table td {
            padding-bottom: 40px;
        }

        .invoice-box table tr.heading td {
            background: #eee;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }

        .invoice-box table tr.details td {
            padding-bottom: 20px;
        }

        .invoice-box table tr.item td{
            border-bottom: 1px solid #eee;
        }

        .invoice-box table tr.item.last td {
            border-bottom: none;
        }

        .invoice-box table tr.total td:nth-child(2) {
            border-top: 2px solid #eee;
            font-weight: bold;
        }

        @media only screen and (max-width: 600px) {
            .invoice-box table tr.top table td {
                width: 100%;
                display: block;
                text-align: center;
            }

            .invoice-box table tr.information table td {
                width: 100%;
                display: block;
                text-align: center;
            }
        }

        /** RTL **/
        .rtl {
            direction: rtl;
            font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        }

        .rtl table {
            text-align: right;
        }

        .rtl table tr td:nth-child(2) {
            text-align: left;
        }
    </style>
</head>

<body>
<div class="invoice-box">
    <table cellpadding="0" cellspacing="0">
        <tr class="top">
            <td colspan="2">
                <table>
                    <tr>
                        <td class="title">
                            <img
                                src="data:image/png;base64,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"
                            >
                        </td>

                        <td>
                            Invoice #: 123<br>
                            Created: January 1, 2015<br>
                            Due: February 1, 2015
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="information">
            <td colspan="2">
                <table>
                    <tr>
                        <td>
                            Sparksuite, Inc.<br>
                            12345 Sunny Road<br>
                            Sunnyville, CA 12345<br>
                            <a href="https://github.com/sparksuite/simple-html-invoice-template">Template source</a>
                        </td>

                        <td>
                            Acme Corp.<br>
                            John Doe<br>
                            john@example.com
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr class="heading">
            <td>
                Payment Method
            </td>

            <td>
                Check #
            </td>
        </tr>

        <tr class="details">
            <td>
                Check
            </td>

            <td>
                1000
            </td>
        </tr>

        <tr class="heading">
            <td>
                Item
            </td>

            <td>
                Price
            </td>
        </tr>

        <tr class="item">
            <td>
                Website design
            </td>

            <td>
                $300.00
            </td>
        </tr>

        <tr class="item">
            <td>
                Hosting (3 months)
            </td>

            <td>
                $75.00
            </td>
        </tr>

        <tr class="item last">
            <td>
                Domain name (1 year)
            </td>

            <td>
                $10.00
            </td>
        </tr>

        <tr class="total">
            <td></td>

            <td>
                Total: $385.00
            </td>
        </tr>
    </table>
</div>
</body>
</html>
