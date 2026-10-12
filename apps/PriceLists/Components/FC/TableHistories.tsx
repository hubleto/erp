import React from 'react'
import Table from '@hubleto/react-ui/components/fc/Table';
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormHistory from './FormHistory';

interface TableHistoriesProps extends TableProps {
  idPriceList?: number,
  idProduct?: number,
}

const TableHistories = (props: TableHistoriesProps) => {
  return <Table
    componentName='TableHistories'
    parentApp='Hubleto/App/Community/PriceLists'
    model='Hubleto/App/Community/PriceLists/Models/History'
    endpointParams={{idPriceList: props.idPriceList, idProduct: props.idProduct}}
    baseUrlSlug='price-lists/histories'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_price_list: props.idPriceList, id_product: props.idProduct}}
    renderForm={(table: TableMeta) => <FormHistory {...table.getDefaultFormProps()} /> }
    {...props}
  ></Table>
}

export default TableHistories;
