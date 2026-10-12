import React from 'react'
import Table from '@hubleto/react-ui/components/fc/Table';
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormVatRate from './FormVatRate';

const TableVatRates = (props: TableProps) => {
  return <Table
    componentName='TableVatRates'
    parentApp='Hubleto/App/Custom/Settings'
    model='Hubleto/App/Custom/Settings/Models/VatRate'
    baseUrlSlug='settings/vat-rates'
    formModalProps={{type: 'right wide'}}
    renderForm={(table: TableMeta) => <FormVatRate {...table.getDefaultFormProps()} /> }
    {...props}
  ></Table>
}

export default TableVatRates;
