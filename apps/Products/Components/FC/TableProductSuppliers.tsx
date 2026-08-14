import React from 'react'
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormProductSupplier from './FormProductSupplier';

export interface TableProductSupplierProps extends TableProps {
  idProduct?: number,
}

const TableProductSuppliers = (props: TableProductSupplierProps) => {
  return <Table
    componentName='TableProductSuppliers'
    parentApp='Hubleto/App/Community/Products'
    model='Hubleto/App/Community/Products/Models/ProductSupplier'
    endpointParams={{idProduct: props.idProduct}}
    formUrlSlug='products/suppliers'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_product: props.idProduct}}
    renderForm={(table: TableMeta) => <FormProductSupplier {...table.getDefaultFormProps()} /> }
    {...props}
  ></Table>
}

export default TableProductSuppliers;
