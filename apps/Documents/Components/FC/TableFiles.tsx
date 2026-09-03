import React from 'react'
import Translator from '@hubleto/react-ui/core/Translator';
import Table from '@hubleto/react-ui/components/fc/Table';
import { TableMeta, TableProps } from '@hubleto/react-ui/components/fc/TableInterfaces';
import FormFile, { FormFileProps } from './FormFile';

interface TableFilesProps extends TableProps {}

const componentName = 'TableFiles'; // must be the same as the exported const
const parentApp = 'Hubleto/App/Community/Documents';
const T = new Translator(parentApp + '/Loader', 'Components/' + componentName);

const TableFiles = (props: TableFilesProps) => {
  return <Table
    componentName={componentName}
    parentApp={parentApp}
    model={parentApp + '/Models/File'}
    formUrlSlug='documents/files'
    formModalProps={{type: 'right wider'}}
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
      return <FormFile {...table.getDefaultFormProps()}/>;
    }}
    {...props}
  ></Table>
}

export default TableFiles;
