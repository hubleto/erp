import React from 'react'
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormProduct from './FormProduct';

interface TableProductsProps extends TableProps {
  idCategory?: number,
}

const TableProducts = (props: TableProductsProps) => {
  return <Table
    componentName='TableProducts'
    parentApp='Hubleto/App/Community/Products'
    model='Hubleto/App/Community/Products/Models/Product'
    endpointParams={{idCategory: props.idCategory}}
    formUrlSlug='products'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_category: props.idCategory}}
    renderForm={(table: TableMeta) => <FormProduct {...table.getDefaultFormProps()} /> }
    {...props}
  ></Table>
}

export default TableProducts;
