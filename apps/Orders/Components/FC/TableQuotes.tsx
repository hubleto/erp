import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormQuote, { FormQuoteProps } from './FormQuote';

interface TableQuotesProps extends TableProps {
  idOrder?: number,
}

const componentName = 'TableQuotes'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Orders';

const TableQuotes = (props: TableQuotesProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Quote'}
    endpointParams={{idOrder: props.idOrder}}
    formUrlSlug='parent-app-slug/same-url-slug-as-in-form'
    formModalProps={{type: 'right wide'}}
    formDefaultValues={{id_order: props.idOrder}}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormQuote {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableQuotes;
