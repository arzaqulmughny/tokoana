import axios from "axios";
import dateFormatter from "./dateFormatter";
import pdfMake from "pdfmake/build/pdfmake";
import pdfFonts from "pdfmake/build/vfs_fonts";
pdfMake.vfs = pdfFonts.pdfMake.vfs;

export default async function printStockInHistoryById(id, name) {
    const { data } = await axios.get(`/history/in/${id}?mode=print`);
    const items = data.items.map(item => [
        item.details.barcode,
        item.details.name,
        item.quantity,
        item.details.unit.name,
        item.details.category.name
    ]);

    const docDefinition = {
        pageSize: "A4",
        pageOrientation: "landscape",
        pageMargins: [50, 50, 50, 50],
        defaultStyle: {
            fontSise: 12,
            lineHeight: 1.4
        },
        images: {
            logo: {
                url: `http://${window.location.host}/assets/images/logo.png`
            }
        },
        content: [
            {
                columns: [
                    {
                        width: "*",
                        columns: [
                            {
                                image: `logo`,
                                width: 58
                            },
                            {
                                layout: "noBorders",
                                table: {
                                    headerRows: 1,
                                    widths: ["auto"],
                                    body: [[{ text: "TOKOANA", fontSize: 18, bold: true }], ["Point of Sales App"]]
                                }
                            }
                        ]
                    },
                    {
                        width: "auto",
                        layout: "noBorders",
                        table: {
                            headerRows: 1,
                            widths: [100, "auto"],
                            body: [
                                [
                                    "Date",
                                    {
                                        text: `${dateFormatter(data.created_at)}`,
                                        color: "#5D5D5D"
                                    }
                                ],
                                ["Printed on", { text: `${dateFormatter(Date.now())}`, color: "#5D5D5D" }],
                                ["Printed by", { text: name, color: "#5D5D5D" }]
                            ]
                        }
                    }
                ],
                columnGap: 10
            },
            {
                canvas: [{ type: "line", x1: 0, y1: 20, x2: 730, y2: 20, lineWidth: 0.1, lineColor: "#A9A9A9" }]
            },
            {
                text: "\nStock In Details",
                fontSize: 18,
                bold: true
            },
            {
                layout: "noBorders",
                table: {
                    headerRows: 1,
                    widths: ["auto", "auto"],
                    body: [
                        ["Supplier", { text: data.supplier !== null ? data.supplier.name : "-", color: "#5D5D5D" }],
                        ["Receiver", { text: data.user.name, color: "#5D5D5D" }],
                        ["Total items", { text: data.items.length, color: "#5D5D5D" }],
                        ["Note", { text: data.note, color: "#5D5D5D" }]
                    ]
                }
            },
            {
                text: "\n"
            },
            {
                layout: "custom1",
                table: {
                    headerRows: 1,
                    widths: ["*", "*", "*", "*", "*"],
                    body: [["BARCODE", "NAME", "QUANTITY", "UNIT", "CATEGORY"], ...items]
                }
            }
        ]
    };

    const tableLayouts = {
        custom1: {
            hLineWidth: function () {
                return 1;
            },
            vLineWidth: function () {
                return 1;
            },
            hLineColor: function () {
                return "#A9A9A9";
            },
            vLineColor: function () {
                return "#A9A9A9";
            },
            paddingLeft: function () {
                return 5;
            },
            paddingRight: function () {
                return 5;
            },
            paddingTop: function () {
                return 5;
            },
            paddingBottom: function () {
                return 5;
            }
        }
    };
    pdfMake.createPdf(docDefinition, tableLayouts).open();
}
