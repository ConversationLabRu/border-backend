const path = require('path');

module.exports = {
    entry: './resources/js/app.jsx',
    output: {
        path: path.resolve(__dirname, 'dist'),
        filename: 'bundle.js',
    },
    module: {
        rules: [
            {
                test: /\.jsx?$/, // Обрабатываем файлы .js и .jsx
                exclude: /node_modules/,
                use: {
                    loader: 'babel-loader',
                },
            },
            // Другие правила
        ],
    },
    resolve: {
        extensions: ['.js', '.jsx'], // Разрешаем расширения .js и .jsx
    },
};
