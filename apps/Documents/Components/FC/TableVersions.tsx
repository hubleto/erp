import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormVersion, { FormVersionProps } from './FormVersion';

interface TableVersionsProps extends TableProps {
  idDocument?: number,
}

const componentName = 'TableVersions'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Documents';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableVersions = (props: TableVersionsProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/Version'}
    endpointParams={{idDocument: props.idDocument}}
    formUrlSlug='documents/versions'
    formModalProps={{type: 'right wider'}}
    formDefaultValues={{id_document: props.idDocument}}
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
      return <FormVersion {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableVersions;
