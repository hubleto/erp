import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormDocument, { FormDocumentProps } from './FormDocument';

interface TableDocumentsProps extends TableProps {
  // Delete or change, if your table shall be filterable
  // by some field. Check prepareReadQuery() in model's
  // record manager if appropriate filtering is applied.
  idSomeField?: number,
}

const componentName = 'TableDocuments'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Documents';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableDocuments = (props: TableDocumentsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Document'}
    endpointParams={{idSomeField: props.idSomeField}}
    formUrlSlug='documents'
    formModalProps={{type: 'right wider'}}
    formDefaultValues={{id_some_field: props.idSomeField}}
    // getRowClassName={(table: TableMeta, rowData: any): string => { return table.getDefaultRowClassName(rowData); }}
    renderCell={(table: TableMeta, columnName: string, column: any, data: any, options: any) => {
      if (columnName == "hyperlink") {
        return <>
          {data[columnName] && data[columnName].length > 28 ? data[columnName].substring(0, 28) + '...' : data[columnName]}
          <a
            href={data[columnName]}
            target='_blank'
            onClick={(e) => { e.stopPropagation(); }}
            className="btn btn-transparent"
          >
            <span className="icon"><i className="fa-solid fa-up-right-from-square"></i></span>
          </a>
        </>;
      } else {
        return table.renderDefaultCell(columnName, column, data, options);
      }
    }}
    renderForm={(table: TableMeta): React.JSX.Element => {
      return <FormDocument {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableDocuments;
