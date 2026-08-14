import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormShop from './FormShop';

const componentName = 'TableShops'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Shops';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableShops = (props: TableProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Shop'}
    formUrlSlug='shops'
    formModalProps={{type: 'right wide'}}
    renderForm={(table: TableMeta) => <FormShop {...table.getDefaultFormProps()}/>}
    {...props}
  ></Table>
}

export default TableShops;
