import axios from "axios";
import dateFormatter from "./dateFormatter";
import pdfMake from "pdfmake/build/pdfmake";
import pdfFonts from "pdfmake/build/vfs_fonts";
pdfMake.vfs = pdfFonts.pdfMake.vfs;

export default async function printReceipt(id) {
    const { data } = await axios.get(`/history/sales/${id}`);
    const items = data.items.map((item, index) => {
        return {
            layout: "noBorders",
            table: {
                headerRows: 1,
                widths: ["auto", "auto"],
                body: [
                    [`[${index + 1}]`, item.detail.name],
                    ["", `${item.quantity} X ${item.current_price} = ${item.amount}`]
                ]
            }
        };
    });
    const docDefinition = {
        pageSize: { width: 283.46, height: "auto" },
        content: [
            { text: "TOKOANA", bold: true, fontSize: 14 },
            { text: "POINT OF SALES APP" },
            {
                text: "-------------------------------------------------------------------------"
            },
            { text: `#${data.id}` },
            { text: dateFormatter(data.created_at) },
            {
                columns: [{ text: "CASHIER" }, { text: data.user.name, alignment: "right" }]
            },
            {
                text: "-------------------------------------------------------------------------"
            },
            ...items,
            {
                text: "-------------------------------------------------------------------------"
            },
            {
                columns: [
                    { text: "SUBTOTAL", bold: true },
                    {
                        text: data.amount,
                        alignment: "right",
                        bold: true
                    }
                ]
            },
            {
                columns: [
                    { text: "TOTAL", bold: true },
                    {
                        text: data.amount,
                        alignment: "right",
                        bold: true
                    }
                ]
            },
            {
                columns: [
                    { text: "CASH", bold: true },
                    { text: data.cash, alignment: "right", bold: true }
                ]
            },
            {
                columns: [
                    { text: "CHANGE", bold: true },
                    {
                        text: data.change,
                        alignment: "right",
                        bold: true
                    }
                ]
            },
            {
                text: "-------------------------------------------------------------------------"
            },
            {
                text: "THANK YOU FOR PURCHASING!",
                alignment: "center"
            }
        ],
        defaultStyle: {
            fontSise: 12
        },
        pageMargins: [20, 20, 20, 20]
    };
    pdfMake.createPdf(docDefinition, null).open();
}
