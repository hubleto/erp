import React from 'react'
import Table from '@hubleto/react-ui/components/fc/Table';
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormResolution from './FormResolution';

interface TableResolutionsProps extends TableProps {
  idPriceList?: number,
  idProduct?: number,
  parentModel?: string,
  idParentRecord?: number,
}

const TableResolutions = (props: TableResolutionsProps) => {
  return <Table
    componentName='TableResolutions'
    parentApp='Hubleto/App/Community/PriceLists'
    model='Hubleto/App/Community/PriceLists/Models/Resolution'
    endpointParams={{idPriceList: props.idPriceList, idProduct: props.idProduct, parentModel: props.parentModel, idParentRecord: props.idParentRecord}}
    baseUrlSlug='price-lists/resolutions'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_price_list: props.idPriceList, id_product: props.idProduct}}
    renderForm={(table: TableMeta) => <FormResolution {...table.getDefaultFormProps()} /> }
    {...props}
  ></Table>
}

export default TableResolutions;
