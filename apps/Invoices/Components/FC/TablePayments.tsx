import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormPayment, { FormPaymentProps } from './FormPayment';

interface TablePaymentsProps extends TableProps {
  idInvoice?: number,
}

const componentName = 'TablePayments'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Invoices';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TablePayments = (props: TablePaymentsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Payment'}
    endpointParams={{idInvoice: props.idInvoice}}
    formUrlSlug='invoices/payments'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_invoice: props.idInvoice}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormPayment {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TablePayments;
