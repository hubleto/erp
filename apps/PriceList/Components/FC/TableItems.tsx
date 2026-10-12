import React from 'react'
import Table from '@hubleto/react-ui/components/fc/Table';
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormItem from './FormItem';

interface TableItemsProps extends TableProps {
  idPriceList?: number,
  idProduct?: number,
}

const TableItems = (props: TableItemsProps) => {
  return <Table
    componentName='TableItems'
    parentApp='Hubleto/App/Custom/PriceList'
    model='Hubleto/App/Custom/PriceList/Models/Item'
    endpointParams={{idPriceList: props.idPriceList, idProduct: props.idProduct}}
    baseUrlSlug='price-lists/items'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_price_list: props.idPriceList, id_product: props.idProduct}}
    renderForm={(table: TableMeta) => <FormItem {...table.getDefaultFormProps()} /> }
    {...props}
  ></Table>
}

export default TableItems;
