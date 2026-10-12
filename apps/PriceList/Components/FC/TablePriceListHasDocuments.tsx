import React from 'react'
import Table from '@hubleto/react-ui/components/fc/Table';
import { type TableMeta, type TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormPriceListHasDocument from './FormPriceListHasDocument';

interface TablePriceListHasDocumentsProps extends TableProps {
  idPriceList?: number,
}

const TablePriceListHasDocuments = (props: TablePriceListHasDocumentsProps) => {
  return <Table
    componentName='TablePriceListHasDocuments'
    parentApp='Hubleto/App/Custom/PriceList'
    model='Hubleto/App/Custom/PriceList/Models/PriceListHasDocument'
    endpointParams={{idPriceList: props.idPriceList}}
    baseUrlSlug='price-lists/documents'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_price_list: props.idPriceList}}
    renderForm={(table: TableMeta) => <FormPriceListHasDocument {...table.getDefaultFormProps()} /> }
    {...props}
  ></Table>
}

export default TablePriceListHasDocuments;
