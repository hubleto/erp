import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormInvoice from './FormInvoice';
import moment from "moment";

const componentName = 'TableInvoices'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Invoices';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableInvoices = (props: TableProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Invoice'}
    baseUrlSlug='invoices'
    formModalProps={{type: 'right wide'}}
    getCellClassName={(table: TableMeta, columnName: string, column: any, rowData: any): string => {
      const cellClassName = table.getDefaultCellClassName(columnName, column, rowData);

      if (columnName == 'date_due') {
        const now = moment();
        const daysDue = moment(now).diff(moment(rowData['date_due']), 'days');
        const datePayment = moment(rowData['date_payment']);

        if (!datePayment.isValid()) {
          if (daysDue >= 0) return cellClassName + ' bg-red-200 text-red-800';
          else if (daysDue > -7) return cellClassName + ' text-yellow-800';
          else return cellClassName;
        } else {
          return cellClassName;
        }
      } else {
        return cellClassName;
      }
    }}
    renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => {
      if (columnName == "date_sent" && !data['date_sent']) {
        return <div className='badge badge-danger'>{T.translate('Not sent')}</div>;
      } else if (columnName == "date_payment" && !data['date_payment']) {
        const now = moment();
        const dateDue = moment(data['date_due']);
        const badgeColor = (now > dateDue ? 'danger' : 'warning');
        return <div className={'badge badge-' + badgeColor}>{T.translate('Not paid')}</div>;
      } else if (columnName == "virt_items") {
        try {
          let items = JSON.parse(data['virt_items']);
          return <div className='flex flex-col'>{items.map((item, index) => {
            return <div key={index} className='badge text-xs'>
              {item.item}: {item.amount ?? 0} {T.translate('ks')} x {globalThis.hubleto.currencyFormat(item.unit_price)} €
            </div>
          })}</div>;
        } catch (ex) {
          return null;
        }
      } else if (columnName == "virt_payments") {
        try {
          let payments = JSON.parse(data['virt_payments']);
          return <div className='flex flex-col'>{payments.map((payment, index) => {
            return <div key={index} className='badge text-xs'>
              {payment.date_payment} {payment.amount ?? 0} €
            </div>
          })}</div>;
        } catch (ex) {
          return null;
        }
      } else {
        return table.renderDefaultCell(columnName, column, data, options);
      }
    }}
    renderCard={(table, row) => {
      const inbound = Number(row.inbound_outbound) === 1;
      // const statusColumn = table.description?.columns.status;
      // const statusClass = statusColumn?.enumCssClasses?.[row.status] || '';
      // const statusLabel = row['_ENUM[status]'] || statusColumn?.enumValues?.[row.status] || T.translate('Unknown');

      const now = moment();
      const dateDue = moment(row.date_due);
      const paidNotPaidBadge = row.date_payment
        ? <span className={'badge badge-success'}>{T.translate('Paid')}</span>
        : <span className={'badge badge-' + (now > dateDue ? 'danger' : 'warning')}>{T.translate('Not paid')}</span>
      ;

      const SUPPLIER = row.SUPPLIER;
      const CUSTOMER = row.CUSTOMER;
      const CURRENCY = row.CURRENCY;

      return <article key={row.id} className='card m-2 min-w-0 max-w-2xl flex-1 basis-80 overflow-hidden flex flex-col'>
        <div className='card-header gap-2'>
          <span className='text-xs uppercase tracking-wider'><i className={`fas ${inbound ? 'fa-arrow-down' : 'fa-arrow-up'} mr-2`} />{T.translate(inbound ? 'Inbound' : 'Outbound')}</span>
          {paidNotPaidBadge}
        </div>
        <div className='card-body p-4'>
          <a className='block text-lg font-bold break-words' href={`${globalThis.hubleto.config.projectUrl}/einvoice/${row.id}`} onClick={(event) => {
            if (event.ctrlKey || event.metaKey) return;
            event.preventDefault();
            if (!row._PERMISSIONS || row._PERMISSIONS[1]) table.openForm(row.id);
          }}>{row.number || `#${row.id}`}</a>
          <div className='font-semibold break-words'>{inbound ? SUPPLIER?.name : CUSTOMER?.name}</div>
          <div className='text-sm text-gray-500 mb-4'>{inbound ? CUSTOMER?.name : SUPPLIER?.name}</div>
          <div className='rounded bg-primary/10 p-3 mb-4'>
            <div className='text-xs text-gray-500'>{T.translate('Total incl. VAT')}</div>
            <div className='text-2xl font-bold'>{globalThis.hubleto.formatCurrency(row.total_incl_vat, CURRENCY?.code)}</div>
            <div className='text-xs text-gray-500'>{T.translate('Excl. VAT')}: {globalThis.hubleto.formatCurrency(row.total_excl_vat, CURRENCY?.code)}</div>
          </div>
          {/* <div className='flex flex-wrap gap-x-4 gap-y-1 text-xs text-gray-500 mb-4'>
            {summary.issued ? <span>{T.translate('Issued')}: {summary.issued}</span> : null}
            {summary.due ? <span>{T.translate('Due')}: {summary.due}</span> : null}
          </div> */}
          {/* <EinvoiceItems summary={summary} limit={4} /> */}
        </div>
        <div className='card-footer'>{table.props.renderActionsColumn?.(table, row)}</div>
      </article>;
    }}
    renderFooter={(table: TableMeta) => {
      let totalExclVat = 0;
      let totalVat = 0;
      let totalInclVat = 0;

      for (let i in table.data?.records) {
        const row = table.data?.records[i];
        totalExclVat += parseFloat(row['total_excl_vat']);
        totalVat += parseFloat(row['total_incl_vat']) - parseFloat(row['total_excl_vat']);
        totalInclVat += parseFloat(row['total_incl_vat']);
      }

      return <div className="font-bold">
        {T.translate('Total excl. VAT')}: {totalExclVat.toFixed(2)} €<br/>
        {T.translate('Total VAT')}: {totalVat.toFixed(2)} €<br/>
        {T.translate('Total incl. VAT')}: {totalInclVat.toFixed(2)} €
      </div>;
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormInvoice {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableInvoices;
