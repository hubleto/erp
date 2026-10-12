import React from 'react'
import Table from '@hubleto/react-ui/components/fc/Table';
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormCurrencyRate from './FormCurrencyRate';

const TableCurrencyRates = (props: TableProps) => {
  return <Table
    componentName='TableCurrencyRates'
    parentApp='Hubleto/App/Custom/Settings'
    model='Hubleto/App/Custom/Settings/Models/CurrencyRate'
    baseUrlSlug='settings/currency-rates'
    formModalProps={{type: 'right wide'}}
    renderForm={(table: TableMeta) => <FormCurrencyRate {...table.getDefaultFormProps()} /> }
    {...props}
  ></Table>
}

export default TableCurrencyRates;
